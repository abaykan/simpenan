#!/bin/bash
# Usage: ./extract-endpoints-from-js.sh /tmp/l.txt

GREEN='\033[0;32m'  # ANSI escape sequence for green color
NC='\033[0m'       # ANSI escape sequence to reset color

echo -e " /\_/\  "
echo -e "( o.o )  Extract Endpoint(s) from JS Files~"
echo -e " > ^ < "
echo "optional: -o output.txt"
echo -e "---------------------------------------------"

# Check if the input file argument is provided
if [ -z "$1" ]; then
  echo "Please provide the path to the input file."
  exit 1
fi
echo -e "\nList: $1\n"
# Assign the input file argument to a variable
input_file="$1"

# Check if the -o option is provided with a filename
if [ "$2" == "-o" ] && [ ! -z "$3" ]; then
  output_file="$3"
else
  random_string=$(head /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 10)
  output_file="/tmp/$random_string.txt"
fi

# Read the input file line by line
while IFS= read -r line; do
  echo -e "${GREEN}URL${NC}: $line"
  # Perform curl command with the line as a parameter and pipe the output to extract.rb
  output=$(curl -s "$line" | grep -oh "\"\/[a-zA-Z0-9_/?=&]*\"" | sed -e 's/^"//' -e 's/"$//' | sort -u)

  # Check if the output contains a forward slash (/)
  if [[ $output == *"/"* ]]; then
    output_length=${#output}
    if (( output_length <= 500 )); then
      echo "$output" | tee -a "$output_file"
    fi
  fi
done < "$input_file"

echo -e "\n${GREEN}Output File${NC}: $output_file"
