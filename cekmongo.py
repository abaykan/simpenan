#!/usr/bin/env python3
# project  : cekmongo
# usage    : ./cekmongo.py -l list-ip-or-domain.txt
# desc	   : MongoDB by default does not enforce authentication. In many situations, this may allow anyone on the network to access all data within the database.
# author   : akbar@kustirama.id

import argparse, sys
from colorama import Fore, Style
from concurrent.futures import ThreadPoolExecutor
from pymongo import MongoClient
from pymongo.errors import ConnectionFailure

print (f'''
{Fore.GREEN}( ͡❛ ‿‿ ͡❛){Style.RESET_ALL} cekmongo
    github.com/abaykan
''')

parser = argparse.ArgumentParser()
parser.add_argument('-l', '--list', required=True, help="List IP/Domain(s)")
parser.add_argument('-t', '--thread', required=False, help="Thread(s) Num")
args = parser.parse_args()

def get_database(host):
    try:
        client = MongoClient(host=host, serverSelectionTimeoutMS=3000)
        dbs = len(client.list_database_names())
        print(f"{Fore.GREEN}%s{Style.RESET_ALL} - %s Databases" % (host, dbs))
    except ConnectionFailure:
        print("%s - error" % (host))
	
def main():
    if args.thread:
        thread_num = int(args.thread)
    else:
        thread_num = 5

    if args.list:
        list_host = open(args.list, 'r')
        futures = []
        output = []

        with ThreadPoolExecutor(max_workers=thread_num) as executor:
            for line in list_host:
                if len(line) < 3:
                    continue

                future = executor.submit(get_database, line.rstrip())
                futures.append(future)

            for future in futures:
                try:
                    result = ''.join(filter(str.isalnum, future.result()))
                    output.append(result)
                    print(output)
                except TypeError:
                    pass
        
        list_host.close()

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('\nInterrupted')
        sys.exit(0)
