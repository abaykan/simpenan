#!/usr/bin/env python3
# project  : indexe
# desc	   : Ambil informasi mahasiswa dari Kemdikbud
# author   : abay@codelatte.org
# usage    : ./mahasiswa.py -m [NAMA/NIM] [-d/--detail]

import re, sys, requests, argparse, json

print (''' _	 _			 
|_|___ _| |___ _ _ ___ 
| |   | . | -_|_'_| -_|
|_|_|_|___|___|_,_|___|
          codelatte.net
''')

parser = argparse.ArgumentParser()
parser.add_argument('-m', '--mahasiswa', required=True, help="Name/NIM")
parser.add_argument('-d', '--detail', action='store_true', help="Show detailed information for each student")
args = parser.parse_args()

def mahasiswaDetail(keyword):
	print(keyword)

def get_student_detail(student_id):
	"""Fetch detailed information for a specific student using their ID"""
	headers = {
		'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
		'Origin': 'https://pddikti.kemdiktisaintek.go.id',
		'Referer': 'https://pddikti.kemdiktisaintek.go.id/',
		'Accept': 'application/json',
		'Accept-Language': 'en-US,en;q=0.9,id;q=0.8',
		'Accept-Encoding': 'gzip, deflate, br',
		'Connection': 'keep-alive',
	}
	
	try:
		x = requests.get(f"https://api-pddikti.kemdiktisaintek.go.id/detail/mhs/{student_id}", headers=headers).text
		detail_data = json.loads(x)
		return detail_data
	except Exception as e:
		print(f"Error fetching detail for ID {student_id}: {e}")
		return None

def mahasiswa(keyword, show_detail=False):
	print('Searching for "' + keyword + '"\n')
	headers = {
		'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
		'Origin': 'https://pddikti.kemdiktisaintek.go.id',
		'Referer': 'https://pddikti.kemdiktisaintek.go.id/',
		'Accept': 'application/json',
		'Accept-Language': 'en-US,en;q=0.9,id;q=0.8',
		'Accept-Encoding': 'gzip, deflate, br',
		'Connection': 'keep-alive',
	}

	x = requests.get("https://api-pddikti.kemdiktisaintek.go.id/pencarian/mhs/" + keyword, headers=headers).text
	output = json.loads(x)

	if not output:
		print("Not found!")
		return

	# Handle different response formats
	if isinstance(output, dict):
		# If it's a dict, check if it contains a list of results
		if 'data' in output:
			students = output['data']
		else:
			print("Unexpected response format!")
			return
	elif isinstance(output, list):
		# If it's already a list, use it directly
		students = output
	else:
		print("Unexpected response format!")
		return

	print(f"Found {len(students)} result(s):\n")
	
	for student in students:
		if show_detail:
			# Fetch and display detailed information
			detail = get_student_detail(student['id'])
			if detail:
				print("Name\t\t: " + detail.get('nama', 'N/A'))
				print("NIM\t\t: " + detail.get('nim', 'N/A'))
				print("University\t: " + detail.get('nama_pt', 'N/A'))
				print("Program\t\t: " + detail.get('prodi', 'N/A'))
				print("Level\t\t: " + detail.get('jenjang', 'N/A'))
				print("Gender\t\t: " + ("Male" if detail.get('jenis_kelamin') == 'L' else "Female"))
				print("Status\t\t: " + detail.get('status_saat_ini', 'N/A'))
				print("Entry Date\t: " + detail.get('tanggal_masuk', 'N/A'))
				print("Registration Type: " + detail.get('jenis_daftar', 'N/A'))
				print("Detail URL\t: https://pddikti.kemdiktisaintek.go.id/detail-mahasiswa/" + student['id'])
				print("")
			else:
				# Fallback to basic info if detail fetch fails
				print("Name\t\t: " + student['nama'])
				print("NIM\t\t: " + student['nim'])
				print("Lembaga\t\t: " + student['nama_pt'])
				print("Prodi\t\t: " + student['nama_prodi'])
				print("Detail URL\t: https://pddikti.kemdiktisaintek.go.id/detail-mahasiswa/" + student['id'])
				print("")
		else:
			# Display summary information
			print("Name\t\t: " + student['nama'])
			print("NIM\t\t: " + student['nim'])
			print("Lembaga\t\t: " + student['nama_pt'])
			print("Prodi\t\t: " + student['nama_prodi'])
			print("Detail URL\t: https://pddikti.kemdiktisaintek.go.id/detail-mahasiswa/" + student['id'])
			print("")
	
if args.mahasiswa:
	mahasiswa(args.mahasiswa, args.detail)
