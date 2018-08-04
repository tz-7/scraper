<?php

namespace Tz7\WebScraper\Browser;


use Exception;
use Symfony\Component\Process\Process;


class PhantomJsRunner
{
    /** @var string */
    private $address;

    /** @var string */
    private $bin;

    /** @var array */
    private $options;

    /** @var int|null */
    private $timeout;

    /**
     * @param string   $address
     * @param string   $bin
     * @param array    $options
     * @param int|null $timeout
     */
    public function __construct($address = '127.0.0.1:8910', $bin = 'phantomjs', array $options = [], $timeout = null)
    {
        $this->address = $address;
        $this->bin     = $bin;
        $this->options = $options;
        $this->timeout = $timeout;
    }

    /**
     * Run server in separate shell script.
     */
    public function run()
    {
        $script = implode(
            ' ',
            array_map(
                [
                    'Symfony\Component\Process\ProcessUtils',
                    'escapeArgument'
                ],
                array_merge(
                    [realpath(__DIR__ . '/../../bin/phantomjs-runner')],
                    [
                        'start',
                        $this->address,
                        $this->bin
                    ]
                )
            )
        );

        $process = new Process('exec ' . $script);
        $process->start();

        for ($i = 0; $i < 3; $i++)
        {
            sleep(1);

            $errorOutput = $process->getErrorOutput();
            if (!empty($errorOutput))
            {
                throw new \RuntimeException($errorOutput);
            }

            if ($process->isRunning())
            {
                break;
            }

            sleep(4);
        }
    }

    /**
     * Start server process forked with lock file.
     *
     * @throws Exception
     */
    public function start()
    {
        if ($this->isRunning())
        {
            throw new Exception(sprintf('%s is already running!', $this->bin));
        }

        $pid = pcntl_fork();

        if ($pid < 0)
        {
            throw new Exception('Unable to start the server process.');
        }

        if ($pid > 0)
        {
            printf("Browser listening on %s\n", $this->address);

            return;
        }

        if (posix_setsid() < 0)
        {
            throw new Exception('Unable to set the child process as session leader');
        }

        if (null === $process = $this->createServerProcess())
        {
            throw new Exception('Unable to create server process.');
        }

        $process->disableOutput();
        $process->start();
        $lockFile = $this->getLockFile();
        touch($lockFile);

        if (!$process->isRunning())
        {
            unlink($lockFile);

            throw new Exception('Unable to start server process.');
        }

        while ($process->isRunning())
        {
            if (!file_exists($lockFile))
            {
                $process->stop();
            }

            sleep(1);
        }
    }

    /**
     * Stop server process by removing the lock file.
     */
    public function stop()
    {
        if ($this->isRunning())
        {
            unlink($this->getLockFile());
        }
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        $lockFile = $this->getLockFile();

        if (file_exists($lockFile))
        {
            return true;
        }

        $pos      = strrpos($this->address, ':');
        $hostname = substr($this->address, 0, $pos);
        $port     = substr($this->address, $pos + 1);

        $fp = @fsockopen($hostname, $port, $errno, $errstr, 5);

        if (false !== $fp)
        {
            fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * @return Process
     */
    protected function createServerProcess()
    {
        $options = array_merge(['webdriver' => $this->address], $this->options);

        $script = implode(
            ' ',
            array_map(
                [
                    'Symfony\Component\Process\ProcessUtils',
                    'escapeArgument'
                ],
                array_merge(
                    [$this->bin],
                    array_map(
                        function ($key, $value)
                        {
                            return '--' . $key . '=' . $value;
                        },
                        array_keys($options),
                        array_values($options)
                    )
                )
            )
        );

        return new Process('exec ' . $script, null, null, $this->timeout);
    }

    /**
     * @return string
     */
    protected function getLockFile()
    {
        return sys_get_temp_dir() . '/phantomjs-' . strtr($this->address, '.:', '--') . '.pid';
    }
}
