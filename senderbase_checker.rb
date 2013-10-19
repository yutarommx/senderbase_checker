#!/usr/bin/ruby
require 'resolv'
require 'ipaddr'
 
SERVER = 'rf.senderbase.org'
RESULTTXT='result.txt'
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
				reputation = response.strings.first.to_f
				ptr = resolver.getname(ip).to_s
				#puts "#{reputation},#{ip},#{ptr}"
				result.push("#{reputation},#{ip},#{ptr}")
				#p result.pop
			rescue
				next
			end

	end
	#printf result
	file = open(RESULTTXT,'w')
	file.puts(result.sort!)
	file.close()

end
