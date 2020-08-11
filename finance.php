<?php

class finance {
   private $data_filename = __DIR__ . "/.cache.dat", $data, $first_data, $second_data, $total, $arguments, $count_arguments;

   private function cache_data () {
      $data = serialize (array_merge ($this->first_data, $this->second_data));
      return file_put_contents ($this->data_filename, $data);
   }

   private function load_data () {
      if (file_exists ($this->data_filename)) {
         $contents = file_get_contents ($this->data_filename);
         $this->data = unserialize ($contents);
      }
      else
         $this->data = [["nominal" => 500, "count" => 0], ["nominal" => 1000, "count" => 0], ["nominal" => 1000, "count" => 0], ["nominal" => 2000, "count" => 0], ["nominal" => 5000, "count" => 0], ["nominal" => 10000, "count" => 0], ["nominal" => 20000, "count" => 0], ["nominal" => 50000, "count" => 0], ["nominal" => 100000, "count" => 0]];
      $this->first_data = array_slice ($this->data, 0, 2);
      $this->second_data = array_slice ($this->data, 2);
   }

   public function revenue () {
      foreach ($this->arguments as $pointer => $argument) {
         $nth = $pointer + 2;

         if (preg_match ("![0-9]+:[0-9]+!", $argument)) {
            list ($nominal, $count) = explode (":", $argument);
            $key = array_search ($nominal, array_column ($this->second_data, "nominal"));
            $is_valid_count = $count > 0;

            if ($key !== false and $is_valid_count) {
               if ($this->confirm ($nominal, $count, true)) {
                  $this->second_data[$key]["count"] += $count;
                  $this->cache_data ();
                  echo "Nominal '$nominal' bertambah '$count'\n";
               }
               else
                  echo "Nominal '$nominal' batal ditambahkan '$count'.\n";
            }

            if ($key === false) fwrite (STDERR, "Peringatan: nominal '$nominal' di parameter ke $nth tidak valid, nominal yang valid adalah 1000, 2000, 5000, 10000, 20000, 50000 dan 100000.\n");
            if (!$is_valid_count) fwrite (STDERR, "Peringatan: jumlah '$count' di parameter ke $nth tidak valid, jumlah yang valid harus lebih besar dari 0.\n");
         }
         elseif (preg_match ("![0-9]+=[0-9]+!", $argument)) {
            list ($nominal, $count) = explode ("=", $argument);
            $key = array_search ($nominal, array_column ($this->first_data, "nominal"));
            $is_valid_count = $count > 0;

            if ($key !== false and $is_valid_count) {
               if ($this->confirm ($nominal, $count, true)) {
                  $this->first_data[$key]["count"] += $count;
                  $this->cache_data ();
                  echo "Nominal '$nominal' bertambah '$count'\n";
               }
               else
                  echo "Nominal '$nominal' batal ditambahkan '$count'.\n";
            }

            if ($key === false) fwrite (STDERR, "Peringatan: nominal '$nominal' di parameter ke $nth tidak valid, nominal yang valid adalah 500 dan 1000.\n");
            if (!$is_valid_count) fwrite (STDERR, "Peringatan: jumlah '$count' di parameter ke $nth tidak valid, jumlah yang valid harus lebih besar dari 0.\n");
         }
         else
            fwrite (STDERR, "Peringatan: argumen '$argument' di parameter ke $nth tidak valid.\n");
      }
   }

   public function spending () {
      foreach ($this->arguments as $pointer => $argument) {
         $nth = $pointer + 2;

         if (preg_match ("![0-9]+:[0-9]+!", $argument)) {
            list ($nominal, $count) = explode (":", $argument);
            $key = array_search ($nominal, array_column ($this->second_data, "nominal"));
            $is_valid_count = $count > 0;

            if ($key !== false and $is_valid_count and $is_sufficient = $this->second_data[$key]["count"] >= $count) {
               if ($this->confirm ($nominal, $count, false)) {
                  $this->second_data[$key]["count"] -= $count;
                  $this->cache_data ();
                  echo "Nominal '$nominal' berkurang '$count'\n";
               }
               else
                  echo "Nominal '$nominal' batal dikurangi '$count'.\n";
            }

            if ($key === false) fwrite (STDERR, "Peringatan: nominal '$nominal' di parameter ke $nth tidak valid, nominal yang valid adalah 1000, 2000, 5000, 10000, 20000, 50000 dan 100000.\n");
            if (!$is_valid_count) fwrite (STDERR, "Peringatan: jumlah '$count' di parameter ke $nth tidak valid, jumlah yang valid harus lebih besar dari 0.\n");
            if (isset ($is_sufficient) and !$is_sufficient) fwrite (STDERR, "Peringatan: jumlah nominal tidak cukup, nominal '$nominal' hanya '{$this->second_data[$key]["count"]}'.\n");
         }
         elseif (preg_match ("![0-9]+=[0-9]+!", $argument)) {
            list ($nominal, $count) = explode ("=", $argument);
            $key = array_search ($nominal, array_column ($this->first_data, "nominal"));
            $is_valid_count = $count > 0;

            if ($key !== false and $is_valid_count and $is_sufficient = $this->first_data[$key]["count"] >= $count) {
               if ($this->confirm ($nominal, $count, false)) {
                  $this->first_data[$key]["count"] -= $count;
                  $this->cache_data ();
                  echo "Nominal '$nominal' berkurang '$count'\n";
               }
               else
                  echo "Nominal '$nominal' batal dikurangi '$count'.\n";
            }

            if ($key === false) fwrite (STDERR, "Peringatan: nominal '$nominal' di parameter ke $nth tidak valid, nominal yang valid adalah 500 dan 1000.\n");
            if (!$is_valid_count) fwrite (STDERR, "Peringatan: jumlah '$count' di parameter ke $nth tidak valid, jumlah yang valid harus lebih besar dari 0.\n");
            if (isset ($is_sufficient) and !$is_sufficient) fwrite (STDERR, "Peringatan: jumlah nominal tidak cukup, nominal '$nominal' hanya '{$this->first_data[$key]["count"]}'.\n");
         }
         else
            fwrite (STDERR, "Peringatan: argumen '$argument' di parameter ke $nth tidak valid.\n");
      }
   }

   private function format_number ($number) {
      $reverse_string = strrev ($number);
      $strings = str_split ($reverse_string, 3);
      $reverse_glued_string = implode (".", $strings);
      return strrev ($reverse_glued_string);
   }

   private function finance_total () {
      $this->total = 0;

      $nominals = array_column ($this->data, "nominal");
      $counts = array_column ($this->data, "count");

      foreach ($nominals as $i => $nominal) {
         $this->total += $nominal * $counts[$i];
      }
      $this->total = $this->format_number ($this->total);
   }

   public function list_finance () {
      $lengths = [strlen($this->data[0]["count"]), strlen($this->data[1]["count"]), strlen ($this->data[2]["count"]), strlen ($this->data[3]["count"]), strlen ($this->data[4]["count"])];
      $space = max ($lengths) + 3;
      echo "\e[1m***\e[0m \e[34;1mFINANCE\e[0m \e[1m***\e[0m\n";
      printf ("    \e[34;1m%-4s\e[0m: \e[1m%-${space}s\e[0m\e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $this->data[0]["nominal"], $this->data[0]["count"], $this->data[5]["nominal"], $this->data[5]["count"]);
      printf ("    \e[34;1m%-4s\e[0m: \e[1m%-${space}s\e[0m\e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $this->data[1]["nominal"], $this->data[1]["count"], $this->data[6]["nominal"], $this->data[6]["count"]);
      printf ("    \e[34;1m%-4s\e[0m: \e[1m%-${space}s\e[0m\e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $this->data[2]["nominal"], $this->data[2]["count"], $this->data[7]["nominal"], $this->data[7]["count"]);
      printf ("    \e[34;1m%-4s\e[0m: \e[1m%-${space}s\e[0m\e[34;1m%-6s\e[0m: \e[1m%s\e[0m\n", $this->data[3]["nominal"], $this->data[3]["count"], $this->data[8]["nominal"], $this->data[8]["count"]);
      printf ("    \e[34;1m%-4s\e[0m: \e[1m%s\e[0m\n\n", $this->data[4]["nominal"], $this->data[4]["count"]);
      $this->finance_total ();
      echo "    \e[34;1mTOTAL\e[0m: \e[1mRp $this->total\e[0m\n";
   }

   private function confirm ($nominal, $count, $type) {
      if ($type)
         $text = "bertambah";
      else
         $text = "berkurang";

      $total = $this->format_number ($nominal * $count);
      while (true) {
         echo "Konfirmasi nominal '$nominal' akan $text '$count' (Rp $total) > ";
         $input = readline ();

         if ($input === "Y" or $input === "y")
            return true;
         elseif ($input === "N" or $input === "n")
            return false;
         else
            echo "\e[1F\e[0J";
      }
   }

   public function __construct ($arguments, $count_arguments) {
      $this->load_data ();
      $this->arguments = array_slice ($arguments, 2);
      $this->count_arguments = $count_arguments;
   }
}

function main ($arguments, $count_arguments) {
   $finance = new finance ($arguments, $count_arguments);

   if ($count_arguments === 1) {
      $finance->list_finance ();
   }
   else {
      switch ($arguments[1]) {
      case "-r": case "--revenue":
         $finance->revenue ();
         break;
      case "-s": case "--spending":
         $finance->spending ();
         break;
      case "-l": case "--list":
         $finance->list_finance ();
         break;
      default:
         fwrite (STDERR, "Eror fatal: argumen pertama '$arguments[1]' tidak valid.\n");
         break;
      }
   }
}

main ($argv, $argc);

?>