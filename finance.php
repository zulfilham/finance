<?php

class Finance {
   private $data = [["nominal" => 500, "count" => 0], ["nominal" => 1000, "count" => 0], ["nominal" => 1000, "count" => 0], ["nominal" => 2000, "count" => 0], ["nominal" => 5000, "count" => 0], ["nominal" => 10000, "count" => 0], ["nominal" => 20000, "count" => 0], ["nominal" => 50000, "count" => 0], ["nominal" => 100000, "count" => 0]];
   private $data_filename = __DIR__ . "/.cache.dat";
   private $total = 0;
   private $succeed, $error_message;

   public function __construct() {
      $this->load_data();
      $this->count_total();
   }

   private function cache_data() {
      file_put_contents($this->data_filename, serialize($this->data));
   }

   private function load_data() {
      if (file_exists($this->data_filename) and $contents = @unserialize(file_get_contents($this->data_filename)))
         $this->data = $contents;
   }

   public function get_data() {
      return $this->data;
   }

   private function count_total() {
      $this->total = 500 * $this->data[0]["count"] + 1000 * $this->data[1]["count"] + 1000 * $this->data[2]["count"] + 2000 * $this->data[3]["count"] + 5000 * $this->data[4]["count"] + 10000 * $this->data[5]["count"] + 20000 * $this->data[6]["count"] + 50000 * $this->data[7]["count"] + 100000 * $this->data[8]["count"];
   }

   public function get_total() {
      return $this->total;
   }

   public function revenue($arguments) {
      $initial_data = $this->data;

      foreach ($arguments as $pointer => $argument) {
         $nth = $pointer + 2;

         if (preg_match("!^(500{1,3}|1000{1,3}|2000{1,2})(=|:)([1-9][0-9]*)$!", $argument, $items)) {
            if ($items[2] === ":") {
               switch ($items[1]) {
                  case "500":
                     $this->data[0]["count"] += $items[3];
                     break;
                  case "1000":
                     $this->data[1]["count"] += $items[3];
                     break;
                  default:
                     $this->succeed = false;
                     $this->error_message = "Eror fatal: nominal '$items[1]' pada parameter ke $nth tidak valid.\n";
                     return null;
               }
            }
            else {
               switch ($items[1]) {
                  case "1000":
                     $this->data[2]["count"] += $items[3];
                     break;
                  case "2000":
                     $this->data[3]["count"] += $items[3];
                     break;
                  case "5000":
                     $this->data[4]["count"] += $items[3];
                     break;
                  case "10000":
                     $this->data[5]["count"] += $items[3];
                     break;
                  case "20000":
                     $this->data[6]["count"] += $items[3];
                     break;
                  case "50000":
                     $this->data[7]["count"] += $items[3];
                     break;
                  case "100000":
                     $this->data[8]["count"] += $items[3];
                     break;
                  default:
                     $this->succeed = false;
                     $this->error_message = "Eror fatal: nominal '$items[1]' pada parameter ke $nth tidak valid.\n";
                     return null;
               }
            }
         }
         else {
            $this->succeed = false;
            $this->error_message = "Eror fatal: argumen '$argument' pada parameter ke $nth tidak valid.\n";
            return null;
         }
      }

      if (empty($arguments)) {
         $this->succeed = false;
         $this->error_message = "Eror fatal: tidak ada argumen pada opsi -r.\n";
      }
      elseif ($this->confirm_revenue($initial_data, $this->data)) {
         $this->succeed = true;
         $this->count_total();
         $this->cache_data();
      }
      else {
         $this->succeed = false;
         $this->error_message = "Pemasukkan uang dibatalkan.\n";
      }
   }

   public function spending($arguments) {
      $initial_data = $this->data;
      $counts = array_column($this->data, "count");

      foreach ($arguments as $pointer => $argument) {
         $nth = $pointer + 2;

         if (preg_match("!^(500{1,3}|1000{1,3}|2000{1,2})(=|:)([1-9][0-9]*)$!", $argument, $items)) {
            if ($items[2] === ":") {
               switch ($items[1]) {
                  case "500":
                     $this->data[0]["count"] >= $items[3] ? $this->data[0]["count"] -= $items[3] : $i = 0;
                     break;
                  case "1000":
                     $this->data[1]["count"] >= $items[3] ? $this->data[1]["count"] -= $items[3] : $i = 1;
                     break;
                  default:
                     $this->succeed = false;
                     $this->error_message = "Eror fatal: nominal '$items[1]' pada parameter ke $nth tidak valid.\n";
                     return null;
               }
            }
            else {
               switch ($items[1]) {
                  case "1000":
                     $this->data[2]["count"] >= $items[3] ? $this->data[2]["count"] -= $items[3] : $i = 2;
                     break;
                  case "2000":
                     $this->data[3]["count"] >= $items[3] ? $this->data[3]["count"] -= $items[3] : $i = 3;
                     break;
                  case "5000":
                     $this->data[4]["count"] >= $items[3] ? $this->data[4]["count"] -= $items[3] : $i = 4;
                     break;
                  case "10000":
                     $this->data[5]["count"] >= $items[3] ? $this->data[5]["count"] -= $items[3] : $i = 5;
                     break;
                  case "20000":
                     $this->data[6]["count"] >= $items[3] ? $this->data[6]["count"] -= $items[3] : $i = 6;
                     break;
                  case "50000":
                     $this->data[7]["count"] >= $items[3] ? $this->data[7]["count"] -= $items[3] : $i = 7;
                     break;
                  case "100000":
                     $this->data[8]["count"] >= $items[3] ? $this->data[8]["count"] -= $items[3] : $i = 8;
                     break;
                  default:
                     $this->succeed = false;
                     $this->error_message = "Eror fatal: nominal '$items[1]' pada parameter ke $nth tidak valid.\n";
                     return null;
               }
            }

            if (isset($i)) {
               $this->succeed = false;
               $this->error_message = "Eror fatal: nominal '{$this->data[$i]["nominal"]}' pada paramter ke $nth hanya '{$this->data[$i]["count"]}', tidak cukup.\n";
               return null;
            }
         }
         else {
            $this->succeed = false;
            $this->error_message = "Eror fatal: argumen '$argument' pada parameter ke $nth tidak valid.\n";
            return null;
         }
      }

      if (empty($arguments)) {
         $this->succeed = false;
         $this->error_message = "Eror fatal: tidak ada argumen pada opsi -s.\n";
      }
      elseif ($this->confirm_spending($initial_data, $this->data)) {
         $this->succeed = true;
         $this->count_total();
         $this->cache_data();
      }
      else {
         $this->succeed = false;
         $this->error_message = "Pengeluaran uang dibatalkan.\n";
      }
   }

   public function get_succeed() {
      return $this->succeed;
   }

   public function get_error_message() {
      return $this->error_message;
   }

   private function confirm_revenue($initial_data, $final_data) {
      $prompt = "\e[1m***\e[0m \e[34;1mFINANCE\e[0m \e[1m***\e[0m\n    \e[34;1mAnda akan menambahkan uang\e[0m:\n";
      $total = 0;

      foreach ($initial_data as $i => $init_data) {
         if ($init_data["count"] !== $final_data[$i]["count"]) {
            $multiplier = $final_data[$i]["count"] - $init_data["count"];
            $prompt .= sprintf("        \e[34;1m%d\e[0m (\e[34;1m%d\e[0m)\n", $init_data["nominal"], $multiplier);
            $total += $init_data["nominal"] * $multiplier;
         }
      }

      $input = readline(sprintf("%s        \e[34;1mTOTAL\e[0m: Rp %s\n\napa anda yakin (Y/n) > ", $prompt, format_number($total)));

      if ($input === "y" or $input === "Y")
         return true;
      else
         return false;
   }

   private function confirm_spending($initial_data, $final_data) {
      $prompt = "\e[1m***\e[0m \e[34;1mFINANCE\e[0m \e[1m***\e[0m\n    \e[34;1mAnda akan mengeluarkan uang\e[0m:\n";
      $total = 0;

      foreach ($initial_data as $i => $init_data) {
         if ($init_data["count"] !== $final_data[$i]["count"]) {
            $multiplier = $init_data["count"] - $final_data[$i]["count"];
            $prompt .= sprintf("        \e[34;1m%d\e[0m (\e[34;1m%d\e[0m)\n", $init_data["nominal"], $multiplier);
            $total += $init_data["nominal"] * $multiplier;
         }
      }

      $input = readline(sprintf("%s        \e[34;1mTOTAL\e[0m: Rp %s\n\napa anda yakin (Y/n) > ", $prompt, format_number($total)));

      if ($input === "y" or $input === "Y")
         return true;
      else
         return false;
   }
}

function format_number($number) {
   $reversed_number = strrev($number);
   $numbers = str_split($reversed_number, 3);
   $reversed_glued_number = implode(".", $numbers);
   return strrev($reversed_glued_number);
}

function list_finance(Finance $finance) {
   $data = $finance->get_data();
   $i = strlen(max(array_column($data, "count"))) + 2;
   echo "\e[1m***\e[0m \e[34;1mFINANCE\e[0m \e[1m***\e[0m\n";
   printf("    \e[34;1m%-4s\e[0m: \e[1m%-${i}s\e[0m \e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $data[0]["nominal"], $data[0]["count"], $data[5]["nominal"], $data[5]["count"]);
   printf("    \e[34;1m%-4s\e[0m: \e[1m%-${i}s\e[0m \e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $data[1]["nominal"], $data[1]["count"], $data[6]["nominal"], $data[6]["count"]);
   printf("    \e[34;1m%-4s\e[0m: \e[1m%-${i}s\e[0m \e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $data[2]["nominal"], $data[2]["count"], $data[7]["nominal"], $data[7]["count"]);
   printf("    \e[34;1m%-4s\e[0m: \e[1m%-${i}s\e[0m \e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $data[3]["nominal"], $data[3]["count"], $data[8]["nominal"], $data[8]["count"]);
   printf("    \e[34;1m%-4s\e[0m: \e[1m%s\e[0m\n\n", $data[4]["nominal"], $data[4]["count"]);
   printf("    \e[34;1mTOTAL\e[0m: \e[1mRp %s\e[0m\n", format_number($finance->get_total()));
}

function error_finance(Finance $finance) {
   fwrite(STDERR, $finance->get_error_message());
   exit(1);
}

function log_data($data, $status)  {
   $log_filename = __DIR__ . "/.cache.log";
   $contents = str_replace("none", $status, $data);

   if (file_put_contents($log_filename, $contents, FILE_APPEND))
      return true;
   else
      return false;
}

function main($arguments, $count_arguments) {
   $finance = new Finance();
   $searches = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "January", "February", "March", "May", "June", "July", "August", "October", "December"];
   $replacements = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Januari", "Februari", "Maret", "Mei", "Juni", "Juli", "Agustus", "Oktober", "Desember"];
   date_default_timezone_set("Asia/Jakarta");
   $date = str_replace($searches, $replacements, date("l, j F Y - H:i:s"));
   $command = "finance " . implode(" ", array_slice($arguments, 1));
   $log_data = "$date -> $command\nstatus: none\n\n";

   if ($count_arguments === 1) {
      list_finance($finance);
      log_data($log_data, "true");
   }
   else {
      switch ($arguments[1]) {
      case "-r":
         $finance->revenue(array_slice($arguments, 2));

         if ($finance->get_succeed()) {
            echo "Pemasukkan uang berhasil dilakukan.\n";
            log_data($log_data, "true");
         }
         else {
            log_data($log_data, "false");
            error_finance($finance);
         }
         break;
      case "-s":
         $finance->spending(array_slice($arguments, 2));

         if ($finance->get_succeed()) {
            echo "Pengeluaran uang berhasil dilakukan.\n";
            log_data($log_data, "true");
         }
         else {
            log_data($log_data, "false");
            error_finance($finance);
         }
         break;
      case "-l":
         list_finance($finance);
         log_data($log_data, "true");
         break;
      case "-h":
echo <<< help
Usage: finance [-lh]
       finance [-s | -r] <format>...

A simple financial manager.

option:
    -r <format>   set money to be a revenue.
    -s <format>   set money to be spending out.

format:
   The format contains 2 arguments: nominal, and count. They are merged
   either with ':' or '=', coin nominals are using ':' while sheet
   nominals are using '='.

   nominal:
      500, 1000, 2000, 5000, 10000, 20000, 50000, 100000.
   count:
      greater than 0.

AUTHOR: Zulfilham\n
help;
         log_data($log_data, "true");
         break;
      default:
         fwrite(STDERR, "Eror fatal: argumen '$arguments[1]' tidak valid.\n");
         log_data($log_data, "error");
         exit(1);
         break;
      }
   }
}

main($argv, $argc);

?>

