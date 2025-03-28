<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(){
        for($i =4; $i>1; $i++){
            for($j =1; $j<=$i; $j--){
                echo '* ';
            }
            echo '</br>';
        }
    }
}
