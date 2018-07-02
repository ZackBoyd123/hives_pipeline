### _hives_pipeline_  
A bash / python pipeline which was desinged to check if any non-viral sequences in the nt & nr databases contained viral sequences.

#### _hives workflow_ 
The ``blast_seqs.sh`` script follows this workflow:  
  
  ##### _nt_ 
1. Build nt database directory, download & unzip all nt files from ncbi.  
2. Pull all sequences used to generate the nt database into a fasta file named: ``nt.fasta`` 
3. Get relevant files from the ncbi website: accession2taxid, nodes.dmp & divisions.dmp.  
4. Use `` convert_blast.py `` to generate a file of accession number, taxids & divisions.  
5. Split this file into: viral, environmental, phages and other and give each their own directory.
6. Use the file generated in step 4 to pull out all fasta sequences in the ``nt.fasta`` file using ``pull_seqs.py``.  
7. Build a blast database of the "other" sequences generated in step 6.  
8. Blast all viral sequences against this new blast database to determine if any viral sequences were in other samples.  
  
  
_n.b: other samples consist of divisions: bacteria, invertebrate, mammal, plants & fungi, primates, rodent, unassigned, vertebrate, and synthetic and chimeric sequences._  
_Author: Zack Boyd_



