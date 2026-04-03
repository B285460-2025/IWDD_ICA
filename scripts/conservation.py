import subprocess
import sys
import json
from datetime import datetime as dt
import os.path


# Use this when we want to exit so we can display error messages or give back filenames
def return_output(data, status):
    print(json.dumps({
        'data': data,
        'status': status}))
    exit()


now = dt.now().strftime('%d_%m_%y_%H_%M_%S')

try:
    with open(sys.argv[1], "r") as infile:
        input_data = infile.read().strip()
        input_data = json.loads(input_data)
except Exception as err:
    return_output(f"Failed to load input data > {err}", 1)

# Put together a string of sequences in fasta format
clustalo_input = ""
for ncbi_id, record in input_data.items():
    prot_and_org = record['description'].split(' ', 1)[1]
    clustalo_input += f">{record['ncbi_id']}| {prot_and_org}\n{record['sequence']}\n"

# Run clustalo via subprocess - had some issues getting this to work and output to file using subprocess.call so using subprocess.run and doing a separate write
try:
    result = subprocess.run(
        ['clustalo', '-i', '-', '--outfmt=msf'],
        input=clustalo_input,
        capture_output=True,
        text=True
    )
    # Write the output to a file - couldn't get this working to the correct place via the -o flag in the subprocess run command
    with open(f"results/alignment_{now}", 'w') as file:
        file.write(result.stdout)

except Exception as err:
    return_output(f"Failed to run clustalo > {err}", 1)

# Use plotcon to visualise sequence conservation
try:
    # Silence stdout and stderr  so that we only pass back the desired dictionary to the php script
    plotcon = subprocess.call(f"plotcon -sequence results/alignment_{now} -graph png -goutfile results/plotcon_plot_{now} -winsize 4", shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    # Check output file has been created
    if not os.path.isfile(f'results/plotcon_plot_{now}.1.png'):
        return_output(f"Oops, there was an issue running plotcon > {err}", 1)

except Exception as e:
    return_output(f"Failed to run plotcon: {e}", 1)

# Return the filenames
output_data = {'alignment_file': f'results/alignment_{now}', 'plotcon_file': f'results/plotcon_plot_{now}.1.png'}
return_output(output_data, 0)