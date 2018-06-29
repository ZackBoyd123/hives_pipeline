#!/bin/bash
#	#	#	#
# Script is hard coded to run in my directory '/home1/boyd01z'
# this currently isn't open to other users, but it can be done in the future
# or can be coded to run somewhere else.
# 
# My python code has not implemented multiprocessing, but this could be added down the 
# line to speed things up.
#
#	Lines: 25	:	X	Are the NT code.
#	Lines: X	:	X	Are the NR code.
#
#	#	#	#

# Code in my home path and the location of where I want to build the NT and NR dbs.
# aswell as where my python scripts live.
nt_path=/home1/boyd01z/db/nt_db
nr_path=/home1/boyd01z/db/nr_db
python_scripts=/home1/boyd01z/PythonScripts

# Make the database directories.
mkdir -p $nt_path
mkdir -p $nr_path

# Download nt database from ncbi, then unzip all the files.
cd $nt_path
wget "ftp://ftp.ncbi.nlm.nih.gov/blast/db/nt.??.tar.gz"
for i in $(ls | grep .tar.gz); do tar -xvf $i; rm -f $i; done

# Pull out all of the sequences used to generate the database into file 'nt.fasta'
blastdbcmd -entry all -db nt -out nt.fasta
cd $(basename $nt_path)

# Pull all the relevant files from ncbi. i.e tax2id, division, and nodes.dmp
wget "ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/new_taxdump/new_taxdump.tar.gz"
wget ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/accession2taxid/nucl_gb.accession2taxid.gz
for i in $(ls | grep .tar.gz); do tar -xvf $i; rm -f $i; done

# Make seperate directories for each different sample then add a text file to each of them.
mkdir phage_seqs virus_seqs environmental_seqs other_seqs
for i in $(ls | grep _seqs); do touch $i/${i%_seqs}"_id.txt" ; done

# Generate the converted accession file using a python script. All described with the -h option specified. 
python $python_bin"/convert_blast.py" --fasta $nt_path"/nt.fasta" --accession "nucl_gb.accession2taxid/nucl_gb.accession2taxid" --nodes "new_taxdump/nodes.dmp"

# Pull out the identifier from the converted accession file, which will be used later to get the
# sequence from the fasta file. Converted accession file can be compressed / removed afterward
file=converted_accession_file.txt
grep "VIRUS" $file >> virus_seqs/virus_id.txt
grep "PHAGE" $file >> phage_seqs/phage_id.txt
grep "ENVIRONMENTAL" $file >> environmental_seqs/environmental_id.txt
cat $file | grep -v "PHAGE" | grep -v "VIRUS" | grep -v "ENVIRONMENTAL" >> other_seqs/other_id.txt
tar -cvf ${file%.txt}".tar" $file

# Take the identifier files, then use this information to pull out their seqs from the big
# fasta file. This is run in each sub-directory. The fasta can be compressed after this.
# The blast database can also be constructed from each resulting fasta file in this for loop.
for i in $(ls -d */ | grep _seqs); do
	cd $i
	python $python_bin"/pull_seqs.py" --fasta $nt_path"/nt.fasta" --accession ${i%_seqs}"_id.txt" --output ${i%_seqs}".fasta"
	cd $(basename $nt_path)
done
tar -cvf $nt_path"/nt.fasta.tar" $nt_path"/nt.fasta" && rm -f $nt_path"/nt.fasta"


#MAKE BLAST DB, OTHER SEQS
#BLASTN VIRUSES AGAINST IT
# " PHAGES
# " ENV 





