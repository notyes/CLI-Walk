<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Walk extends Command
{

    private $line = array( 
                            'North' => array( 'Y', '+' ) ,
                            'East' => array( 'X', '+' ) ,
                            'South' => array( 'Y', '-' ) ,
                            'West' => array( 'X', '-' ) ,
                        );
    private $whiteCase = array( 'L','R','W' );

    public $msgError = '';

    private $path = __DIR__.'/';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'walk {step : step to walk}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add step to walk';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $step = $this->argument('step');
        $stepWalk = $this->splitString($step);
        if ( ! $this->validation($stepWalk) ) {
            $this->line('<fg=red>Error Step is not work '.$this->msgError.'</>');    
            $this->notify("RoBot", "walk.. broken !!! Boom !!!: ".$step, resource_path('icon-bot.png'));
            return false;
        }
        $this->notify("RoBot", "walk.. walk.. walk.. : ".$step, resource_path('icon-bot.png'));
        $this->line('<fg=blue>RUN...... Robot walk to...</>');

        $this->stepRun( $stepWalk );

    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    public function splitString($string='')
    {
        $stringList = str_split($string);
        $list = array();
        $s = '';
        foreach ($stringList as $key => $value
        ) {
            if ( is_numeric( $value ) ) {
                $s .= $value;
                if ( count($stringList) == ($key+1) ) {
                    $list[] = $s;
                }
                continue;
            }
            if ( ! empty( $s ) ) {
                $list[] = $s;
                $s = '';
            }
            $list[] = $value;
            
        }
        return $list;
    }

    function validation($stepWalk='')
    {
        
        foreach ($stepWalk as $key => $value) {
            
            if ( is_numeric($value) ) {
                if ( strtoupper($stepWalk[$key-1]) != 'W' ) {
                    return false;
                }
            }else{
                if ( !in_array( $value, $this->whiteCase ) ) {
                    $this->msgError = ' Robot unknown step "'.$value.'"';
                    return false;
                }
            }
        }
        return true;
    }

    function stepRun( $stepWalk )
    {
        $way['X'] = 0;
        $way['Y'] = 0;

        foreach ($stepWalk as $key => $value) {
            
            switch ( $value ) {
                case 'L':
                        if ( $this->has_prev( $this->line ) ) {
                            prev($this->line);
                        }else{
                            end($this->line);
                        }
                    break;
                case 'R':
                        if ( $this->has_next( $this->line ) ) {
                            next($this->line);
                        }else{
                            reset($this->line);
                        }
                    break;
                case 'W':
                        // set walk
                    break;        
                
                default:
                    
                    if ( is_numeric($value) ) {
                        $current_way = current($this->line);
                        if( $current_way[1] == '+' ){
                            $way[$current_way[0]] = $way[$current_way[0]] + $value;
                        }else{
                            $way[$current_way[0]] = $way[$current_way[0]] - $value;
                        }
                    }
                    break;
            }

        }

        $stringLine = '=> ';
        foreach ($way as $key => $value) {
            $stringLine .= strtoupper($key).': '.$value.' ';
        }

        $this->line('<fg=green>'.$stringLine.' Direction: '.key( $this->line ).'</>');

    }


    function has_next(array $_array)
    {
      return next($_array) !== false ?: key($_array) !== null;
    }

    function has_prev(array $_array)
    {
      return prev($_array) !== false ?: key($_array) !== null;
    }

}
