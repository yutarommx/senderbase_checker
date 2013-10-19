#!/usr/bin/ruby
require 'resolv'
require 'ipaddr'
 
SERVER = 'rf.senderbase.org'

#iplist = open(ARGV.first, "r")

#unless iplist
#	puts "usage: listfile"
#	exit 3
#end


#for iprange in IPAddr.new(iplist)

	iprange = IPAddr.new(ARGV.first) 
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
				puts "#{reputation},#{ip},#{ptr}"
			rescue
				next
			end

	end

#end
