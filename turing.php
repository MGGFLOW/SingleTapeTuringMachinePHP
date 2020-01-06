<?php
/*
 *  Eliseev Pavel, SMTU, 3270
 *
 */
class command{
    public $needle;
    public $replace;
    public $move;
    public $state;
}

class Turing_machine{
    public $commands_file = 'commands.txt';
    public $alphabet = 'abc'; // except ':',',',' '
    public $void_symbol = '#';
    public $in_state;
    public $exit_state;
    public $commands;
    public $tape;
    public $counter;
    public $max_counter = 1000;


    private function loadCommands(){
        $strings = file($this->commands_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($strings as $string) {
            $string = explode(':',$string);
            $state = trim($string[0]);
            $state_commands = $string[1];
            $state_commands = explode(';',$state_commands);
            foreach ($state_commands as $command){
                $this->commands[$state][] = $this->stringToCommand($command);
            }
        }
    }

    private function stringToCommand($string){
        $parts = explode(',',$string);
        $command = new command();
        $command->needle = trim($parts[0]);
        $command->replace = trim($parts[1]);
        $command->move = trim($parts[2]);
        $command->state = trim($parts[3]);

        return $command;
    }

    private function prepareTape(){
        $this->tape = str_split($this->tape);
    }

    private function checkSymbol($symbol){
        if($symbol==$this->void_symbol or strpos($this->alphabet,$symbol)!==false){
            return true;
        }

        return false;
    }

    private function chooseCommand($state,$needle){
        if(isset($this->commands[$state])){
            foreach ($this->commands[$state] as $command){
                if($command->needle==$needle){
                    return $command;
                }
            }
        }

        return false;
    }

    private function makeMove(&$pointer,$move){
        switch ($move){
            case 'L':
                $pointer--;
                break;
            case 'R':
                $pointer++;
                break;
            default:
                break;
        }
    }

    public function run(){
        $this->loadCommands();
        $this->prepareTape();
        $this->counter = 0;

        $state = $this->in_state;
        $i = 0;
        $length = count($this->tape);

        while($state!=$this->exit_state){
            $this->counter++;
            if($i<0){
                $length = array_unshift($this->tape,$this->void_symbol);
                $i = 0;
            }
            if($i==$length) $length = array_push($this->tape,$this->void_symbol);

            $current_symbol = $this->tape[$i];
            if(!$this->checkSymbol($current_symbol)){
                var_dump('Bad symbol');
                echo '<br>';
                return false;
            }
            $command = $this->chooseCommand($state,$current_symbol);

            if($command===false){
                var_dump('Bad command');
                echo '<br>';
                return false;
            }

            $this->tape[$i] = $command->replace;
            $this->makeMove($i,$command->move);
            $state = $command->state;

            if($this->counter==$this->max_counter){
                var_dump('Over Counter');
                echo '<br>';
                return false;
            }
        }

        return $this->tape = trim(implode('',$this->tape),$this->void_symbol);
    }
}

$machine = new Turing_machine();
$machine->commands_file = 'commands.txt'; // путь к файлу с командами
$machine->alphabet = 'abc*'; // алфавит ленты
$machine->void_symbol = '#'; // пустой символ
$machine->max_counter = 1000; // максимальное количество движений по ленте (против зацикливания)
$machine->in_state = '1'; // начальное состояние
$machine->exit_state = '7'; // состояние выхода
$machine->tape = '*aabbbcccaaa'; // строка ленты

var_dump($machine->tape); // выводим входную ленту
echo '<br>';
var_dump($machine->run()); // запускаем машину
echo '<br>';
var_dump($machine->counter); // выводим количество движений по ленте

// commands file:
// q0: <что>,<на что>,<[L | N | R]>,<состояние>;





