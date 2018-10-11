#!/usr/bin/env python3
import pandas as pd
import sys
import glob
import os
# Choose best hit and thenthrow the other hits into a mysql table.

for f in glob.glob('*.txt'):
    file = f
    # Read a tsv using pandas
    df = pd.read_table(file)

    # Add a header to the df
    df.columns = ['Query','Subject','Percentage','Alignment_length','Mismatch',
                  'Gap_Open','Query_start','Query_end','Subject_start','Subject_end',
                  'Evalue','Bitscore','Query_length','Subject_length','Retro','Query_accession',
                  'Query_taxid','Query_division','Query_html','Subject_accession',
                  'Subject_taxid','Subject_division','Subject_html', 'Orto_id']

    # Duplicate df
    # All the viruses which appear more than once
    duplicates = df[df.duplicated(['Subject'], keep=False)]


    # Unique df
    # Remove all the duplicates from the table
    # see: https://stackoverflow.com/questions/37313691/how-to-remove-a-pandas-dataframe-from-another-dataframe
    unique = pd.concat([df, duplicates]).drop_duplicates(keep=False)

    # Make two lists of all the best hits and worst hits
    bad_hits = []
    best_hit = []

    # iterate through the unique values in query column to subset the dataframe
    for i in duplicates['Subject'].unique():
        # Subset the df to be the current string
        frame = duplicates.loc[duplicates['Subject'] == i]

        # This is the index of the row in the df which has the highest % alignment
        max_prct = frame['Alignment_length'].idxmax()

        # Use the above value to subset the dataframe giving a new dataframe
        best_frame = (frame.loc[[max_prct]])
        # Append it to the best list.
        best_hit.append(best_frame)

        # Drop the best hit from the dataframe and store the other values as sub par hits.
        bad_frame = pd.concat([frame, best_frame]).drop_duplicates(keep=False)
        bad_hits.append(bad_frame)


    # Concatenate all the best hits into one df and append it to the unique df.
    best_df = pd.concat(best_hit)
    best_df = pd.concat([best_df, unique])

    # Concatenate all the sub par hits.
    worst_df = pd.concat(bad_hits)

    # Get string to name file.
    # Get first cell from specified column.
    file_name = worst_df.iloc[0]['Query'].split("|")[5]

    if 'noretro' not in file:
        file_name = file_name + '_retro'

    # Write to files, dont print index.
    print(worst_df.to_csv(sep='\t', index=False), file=open(file_name+'_worst.tsv', 'w'))
    print(best_df.to_csv(sep='\t', index=False), file=open(file_name + '_best.tsv', 'w'))

    os.remove(file)
