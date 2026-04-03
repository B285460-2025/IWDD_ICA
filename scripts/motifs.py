import subprocess
import json
import pandas as pd
import sys
import os.path

# Function to return the output to php script 
def return_output(data, status):
    print(json.dumps({
        'data': data,
        'status': status}))
    exit()

# Probably should have some input validation but as this is defined in the php should be ok
filename = sys.argv[1]

# Read input file data into a useable form
try:
    with open(filename, "r") as infile:
        input_data = infile.read().strip()
        input_data = json.loads(input_data)
except Exception as err:
    return_output(f"Failed to load input data > {err}", 1)

# Build empty dataframe to house results
cols = ['SeqName', 'Start', 'End', 'Score', 'Strand', 'Motif']
df = pd.DataFrame(columns=cols)

# Cycle through each item and create a fasta record which is written to a temporary file.
try:
    for ncbi_id, record in input_data.items():
        seq = f">{record['ncbi_id']}\n{record['sequence']}\n"

        seq_file = f"temp/{ncbi_id}.fasta"
        outfile = f"temp/motif_{ncbi_id}.txt"

        with open(seq_file, 'w') as file:
            file.write(seq)
        # Conduct patmatmotif analysis on each sequence and return the output in tsv format
        try:
            subprocess.call(f"patmatmotifs -sequence {seq_file} -outfile {outfile} -rformat excel", shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL) # silence output to stdout becuase this disrupts the orchestrating php script
        except Exception as err:
            return_output(f"failed to run patmatmotif for {ncbi_id} > {err}", 1)

        if not os.path.isfile(outfile):
            return_output(f"Oops, there was an issue running patmatmotifs, an output file for {ncbi_id} wasn't created > {err}", 1)

        # Add output to motif dictionary
        with open(outfile, 'r') as file:
            temp = pd.read_csv(outfile, sep='\t', names=cols, header=0)
            temp['id'] = record['ncbi_id']
            df = pd.concat([df, temp], ignore_index=True)


        # Cleanup the temp files
        subprocess.call(f"rm {seq_file} {outfile}", shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
except Exception as err:
    return_output(f"Failed to conduct motif analysis > {err}", 1)

# Convert df to json and return
df_dict = df.to_dict("index")
return_output(df_dict, 0)
