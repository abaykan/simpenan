#!/bin/bash
# Usage: ./extract-endpoints-from-js.sh /tmp/l.txt

CYAN='\033[0;36m'
GREEN='\033[0;32m'
NC='\033[0m'

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
  echo -e "[${CYAN}extracting_url${NC}] $line"
  # Perform curl command with the line as a parameter and pipe the output to extract.rb
  output=$(curl -s "$line" | grep -oh "\"\/[a-zA-Z0-9_/?=&]*\"" | sed -e 's/^"//' -e 's/"$//' | sort -u)

  # Check if the output contains a forward slash (/)
  if [[ $output == *"/"* ]]; then
    domain=$(echo $line | awk -F/ '{print $1 "//" $3}')
    # check if output have more than one line
    if [[ $output == *$'\n'* ]]; then
      while IFS= read -r outputline; do
        output_length=${#outputline}
        if (( output_length <= 500 )); then
          echo -e "[${GREEN}found${NC}] $domain$outputline"
          echo "$domain$outputline" >> "$output_file"
        fi
      done <<< "$output"
    else
      echo -e "[${GREEN}found${NC}] $domain$output"
      echo "$domain$output" >> "$output_file"
    fi
  fi
done < "$input_file"

echo -e "[${CYAN}result_file${NC}] $output_file"
