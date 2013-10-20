#!/usr/bin/ruby
require 'rubygems'
require 'resolv'
require 'ipaddr'
require 'mysql2'
 
SERVER = 'rf.senderbase.org'
RESULTTXT='result.txt'
DB = Mysql2::Client.new(:host => "localhost", :username => "sbchkr", :password => "password", :database => "senderbase_db")
result = []

unless ARGV.first
	puts "usage: listfile"
	exit 3
end

for iplist in open(ARGV.first, "r")
	
	iprange = IPAddr.new(iplist) 
		iprange.to_range.each do |ip|

			ip = ip.to_string
			resolver = Resolv::DNS.new

			if ip =~ /[^\d.]/
				ip = resolver.getaddress(ip).to_s
			end
 
			begin
				response = resolver.getresource(ip.split('.').reverse.join('.') + '.' + SERVER, Resolv::DNS::Resource::IN::TXT)

				score = response.strings.first.to_f
				ptr = resolver.getname(ip).to_s
				date = Time.now.strftime "%Y-%m-%d %H:%M:%S"

				if score >= 1.5 then
					status = 'Good'
				elsif score <= -2.0 then
					status = 'Poor'
				else
					status = 'Neutral'
				end

				DB.query("replace into senderbase_db.result values (INET_ATON('#{ip}') , '#{ptr}' , '#{score}' , '#{status}' , '#{date}')")

				result.push("#{ip},#{ptr},#{score},#{status},#{date}")
			rescue
				next
			end

	end
	file = open(RESULTTXT,'w')
	file.puts(result)
	file.close()
end
