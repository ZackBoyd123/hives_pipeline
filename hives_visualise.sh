#!/bin/bash

python_bin=/home1/boyd01z/RichardWork/hives_pipeline/scripts
retro_file=/home1/boyd01z/RichardWork/hives_pipeline/retroviruses.txt
blast_data=/home1/boyd01z/db/blast_results/other_in_virus.txt 
mysql_user=root
mysql_password=root

# Convert the blast output format to a format we want to use.
$python_bin"/make_db.py" --retro $retro_file --input $blast_data --output converted_results.txt

#Probably want to put Richard\s script here v
java -jar HiveAddTaxIds.jar sorted_nodes_paths.txt converted_results.txt

# Subset into retro/non-retro viruses above 95 % alignment
$python_bin"/filter_db.py" --percent 95 --retro --input converted_results.txt --output 95_Retro.txt
$python_bin"/filter_db.py" --percent 95 --input converted_results.txt --output 95_No_Retro.txt

# Subset into divisions
mkdir uploads
 
grep \|BACTERIA\| 95_Retro.txt >> uploads/bacteria_95_retro.txt
grep \|BACTERIA\| 95_Retro.txt >> uploads/bacteria_95_noretro.txt

#Environmental 
grep \|ENVIRONMENTAL\| 95_No_Retro.txt >> uploads/environmental_95_noretro.txt
grep \|ENVIRONMENTAL\| 95_Retro.txt >> uploads/environmental_95_retro.txt

#Mammal 
grep \|MAMMAL\| 95_No_Retro.txt >> uploads/mammal_95_noretro.txt
grep \|MAMMAL\| 95_Retro.txt >> uploads/mammal_95_retro.txt

# Phage
grep \|PHAGE\| 95_Retro.txt >> uploads/phage_95_retro.txt
grep \|PHAGE\| 95_No_Retro.txt >> uploads/phage_95_noretro.txt

# Plants & Dungi 
grep \|PLANTS_\&_FUNGI\| 95_Retro.txt >> uploads/plants_95_retro.txt
grep \|PLANTS_\&_FUNGI\| 95_No_Retro.txt >> uploads/plants_95_noretro.txt

#Primates
grep \|PRIMATES\| 95_No_Retro.txt >> uploads/primates_95_noretro.txt
grep \|PRIMATES\| 95_Retro.txt >> uploads/primates_95_retro.txt

# Rodents
grep \|RODENT\| 95_Retro.txt >> uploads/rodent_95_retro.txt
grep \|RODENT\| 95_No_Retro.txt >> uploads/rodent_95_noretro.txt

# Vertebrates
grep \|VERTEBRATE\| 95_No_Retro.txt >> uploads/vertebrate_95_noretro.txt
grep \|VERTEBRATE\| 95_Retro.txt >> uploads/vertebrate_95_retro.txt

# Invertebrates
grep \|INVERTEBRATE\| 95_Retro.txt >> uploads/invert_95_retro.txt
grep \|INVERTEBRATE\| 95_No_Retro.txt >> uploads/invert_95_noretro.txt

## Get the best hits from each new file
cd uploads
$python_bin"/get_best_hit.py"
$python_bin"/upload.py" --user $mysql_user --password $mysql_password
cd ../
rm -rf uploads
