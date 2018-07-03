#!/bin/bash
#	#	#	#
# Script is hard coded to run in my directory '/home1/boyd01z'
# this currently isn't open to other users, but it can be done in the future
# or can be coded to run somewhere else.
# 
# My python code has not implemented multiprocessing, but this could be added down the 
# line to speed things up.
#
#	Lines: 25	:	94	Are the NT code.
#	Lines: 97	:	151	Are the NR code.
#
#	#	#	#

# Code in my home path and the location of where I want to build the NT and NR dbs.
# aswell as where my python scripts live.
nt_path=/home1/boyd01z/db/nt_db
nr_path=/home1/boyd01z/db/nr_db
python_bin=/home1/boyd01z/PythonScripts

# Make the database directories.
mkdir -p $nt_path
mkdir -p $nr_path

# Download nt database from ncbi, then unzip all the files.
cd $nt_path
wget "ftp://ftp.ncbi.nlm.nih.gov/blast/db/nt.??.tar.gz"
for i in $(ls | grep .tar.gz); do tar -xvf $i; rm -f $i; done

# Pull out all of the sequences used to generate the database into file 'nt.fasta'
blastdbcmd -entry all -db nt -out nt.fasta
cd $(dirname $nt_path)

# Pull all the relevant files from ncbi. i.e tax2id, division, and nodes.dmp
mkdir -p accession2taxid_files
wget "ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/new_taxdump/new_taxdump.tar.gz"
wget ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/accession2taxid/nucl_gb.accession2taxid.gz
mkdir new_taxdump
tar -xvf new_taxdump.tar.gz -C new_taxdump && rm -rf new_taxdump.tar.gz
gunzip nucl_gb.accession2taxid.gz
mv nucl_gb.accession2taxid accession2taxid_files

# Make seperate directories for each different sample then add a text file to each of them.
mkdir virus_seqs other_seqs synthetic_chimeric_seqs

# Generate the converted accession file using a python script. All described with the -h option specified. 
python $python_bin"/convert_blast.py" --accession "accession2taxid_files/nucl_gb.accession2taxid" --nodes "new_taxdump/nodes.dmp"
echo "Generated converted accession file: converted_accession_file.txt"

# Pull out the identifier from the converted accession file, which will be used later to get the
# sequence from the fasta file. Converted accession file can be compressed / removed afterward
file=converted_accession_file.txt
grep "VIRUS" $file >> virus_seqs/virus_id.txt
grep "PHAGE" $file >> virus_seqs/virus_id.txt
grep "SYNTHETIC" $file >> synthetic_chimeric_seqs/synthetic_chimeric_id.txt
cat $file | grep -v "PHAGE" | grep -v "VIRUS" | grep -v "SYNTHETIC" >> other_seqs/other_id.txt
tar -zcvf ${file%.txt}".tar.gz" $file && rm -f $file

# Make some variables for the locations of the new created directories.
other_path=$(dirname $nt_path)"/other_seqs"
virus_path=$(dirname $nt_path)"/virus_seqs"

# Take the identifier files, then use this information to pull out their seqs from the big
# fasta file. This is run in each sub-directory.
for i in $(ls -d */ | grep _seqs); do
	cd $i
	python3 $python_bin"/pull_seqs.py" --fasta $nt_path"/nt.fasta" --accession ${i%_seqs}"_id.txt" --output ${i%_seqs}".fasta"
	cd $(dirname $nt_path)
done

# Compress nt_db as it is no longer needed.
echo "Compressing nt_db....."
tar -zcvf $nt_path".tar.gz" $nt_path && rm -rf $nt_path

# Build a blast db of other seqs and viral seqs. 
echo "Building a blastdb of other seqs...."
makeblastdb -in $other_path"/other.fasta" -out $other_path"/db_other" -dbtype nucl
echo "Building a blastdb of viral seqs...."
makeblastdb -in $virus_path"/phage.fasta" -out $virus_path"/db_virus" -dbtype nucl
echo "Built blastdbs"

## Blast other seqs against viral db.
blast_output=$(dirname $nt_path)"/blast_results"
mkdir -p $blast_output
blastn -query $other_path"/other.fasta" -evalue 1e-5 -num_alignments 1 -num_threads 12 -db $virus_path"/db_virus" -out $blast_output"/other_in_viruses.txt" -outfmt 6

# Compress all the subdirectories as they're no longer needed.
tar -zcvf $other_path".tar.gz" $other_path && rm -rf $other_path
tar -zcvf $virus_path".tar.gz" $virus_path && rm -rf $virus_path


#	#	#	#	#	#	#	#	#

#	#	!	N	R	!	#	#	#

#	#	#	#	#	#	#	#	#	

echo "Finished with nt, now processing nr..."
# Go to nr directory and download the nr database and unzip it.
cd $nr_path
wget ftp://ftp.ncbi.nlm.nih.gov/blast/db/nr.??.tar.gz
for i in $(ls *.tar.gz); do tar -zxvf $i; rm -f $i; done

# Pull out all seqs used to generate nr
blastdbcmd -entry all -db nr -out nr.fasta

# cd up one and make relevant subdirectories
cd $(dirname $nr_path)
mkdir nr_virus_seqs nr_other_seqs nr_synthetic_chimeric_seqs

# Get the protein acc2taxid file.
wget ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/accession2taxid/prot.accession2taxid.gz
gunzip prot.accession2taxid.gz
mv prot.accession2taxid accession2taxid_files

# Generate the converted accession file.
python $python_bin"/convert_blast.py" --accession accession2taxid_files/prot.accession2taxid --nodes new_taxdump/nodes.dmp
mv $file nr_converted_accession_file.txt
file=nr_converted_accession_file.txt

# Split the converted file into sub files.
grep "VIRUS" $file >> nr_virus_seqs/virus_id.txt
grep "PHAGE" $file >> nr_virus_seqs/virus_id.txt
grep "SYNTHETIC" $file >> nr_synthetic_chimeric_seqs/synthetic_chimeric_id.txt
cat $file | grep -v "VIRUS" | grep -v "PHAGE" | grep -v "SYNTHETIC" >> nr_other_seqs/other_id.txt
tar -zcvf $file".tar.gz" $file && rm -f $file

# Make vars of above paths
nr_other_path=$(basename $nr_path)"/nr_other_seqs"
nr_virus_path=$(basename $nr_path)"/nr_virus_seqs"

#Pull out the sequences from the big fasta file in each subdir
for i in $(ls nr_*_seqs); do
	cd $i
	python3 $python_bin"/pull_seqs.py" --fasta $nr_path"/nr.fasta" --accession *_id.txt --output ${i%_seqs}".fasta"
	cd $(dirname $nr_path)
done

# Compress the nr db
tar -zcvf $nr_path".tar.gz" $nr_path && rm -rf $nr_path

# Build prot databases.
echo "Building prot db: other seqs"
makeblastdb -in $nr_other_path"/nr_other.fasta" -out $nr_other_path"/db_other" -dbtype prot
echo "Building prot db: viral seqs"
makeblastdb -in $nr_virus_path"/nr_virus.fasta" -out $nr_virus_path"/db_virus" -dbtype prot

# Blast other seqs against viral db
blastn -query $nr_other_path"/nr_other.fasta" -evalue 1e-5 -num_alignments 1 -num_threads 12 -db $nr_virus_path"/db_virus" -out $blast_output"/nr_other_in_virus.txt" -outfmt 6


# Compress the subdirectories
tar -zcvf $nr_virus_path".tar.gz" $nr_virus_path && rm -rf $nr_virus_path
tar -zcvf $nr_other_parh".tar.gz" $nr_other_path && rm -rf $nr_other_path

