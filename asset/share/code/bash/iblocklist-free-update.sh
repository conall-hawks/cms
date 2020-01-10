#!/bin/bash
################################################################################
# Updates PeerBlock blocklists for FREE!                                       #
################################################################################

# (Organization) Pandora
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=aevzidimyvwybzkletsg
gunzip temp.gz
mv temp organization-pandora.p2p

# (Organization) Joost
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=alxugfmeszbhpxqfdits
gunzip temp.gz
mv temp organization-joost.p2p

# (Organization) Apple
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=aphcqvpxuqgrkgufjruj
gunzip temp.gz
mv temp organization-apple.p2p

# (ISP) Time Warner
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=aqtsnttnqmcucwrjmohd
gunzip temp.gz
mv temp isp-time-warner.p2p

# (General) IANA Reserved [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=bcoepfyewziejvcqyhqo
gunzip temp.gz
mv temp general-iana-reserved.p2p

# (ISP) Verizon
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=cdmdbprvldivlqsaqjol
gunzip temp.gz
mv temp isp-verizon.p2p

# (Organization) Steam
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=cnxkgiklecdaihzukrud
gunzip temp.gz
mv temp organization-steam.p2p

# (General) IANA Private [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=cslpybexmxyuacbyuvib
gunzip temp.gz
mv temp general-iana-private.p2p

# (General) Bad Peers [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=cwworuawihqvocglcoss
gunzip temp.gz
mv temp general-bad-peers.p2p

# (General) Web Attacks [CruzIT]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=czvaehmjpsnwwttrdoyl
gunzip temp.gz
mv temp general-web-attacks.p2p

# (General) Bad Porn [iBlocklist]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=dufcxgnbjsdwmwctgfuj
gunzip temp.gz
mv temp general-bad-porn-iblocklist.p2p

# (ISP) Cablevision
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=dwwbsmzirrykdlvpqozb
gunzip temp.gz
mv temp isp-cablevision.p2p

# (Organization) Electronic Arts
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ejqebpcdmffinaetsvxj
gunzip temp.gz
mv temp organization-electronic-arts.p2p

# (Organization) Blizzard Entertainment
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ercbntshuthyykfkmhxc
gunzip temp.gz
mv temp organization-blizzard-entertainment.p2p

# (General) Palevo [Abuse]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=erqajhwrxiuvjxqrrwfj
gunzip temp.gz
mv temp general-palevo.p2p

# (Organization) Ubisoft
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=etmcrglomupyxtaebzht
gunzip temp.gz
mv temp organization-ubisoft.p2p

# (Organization) Crowd Control Productions
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=eveiyhgmusglurfmjyag
gunzip temp.gz
mv temp organization-crowd-control-productions.p2p

# (General) Forum Spam [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ficutxiwawokxlcyoeye
gunzip temp.gz
mv temp general-forum-spam.p2p

# (Organization) Activision
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=gfnxlhxsijzrcuxwzebb
gunzip temp.gz
mv temp organization-activision.p2p

# (General) Web Exploits [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ghlzqtqxnzctvvajwwag
gunzip temp.gz
mv temp general-web-exploits.p2p

# (General) Unallocated Address Space [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=gihxqmhyunbxhbmgqrla
gunzip temp.gz
mv temp general-unallocated-address-space-bluetack.p2p

# (ISP) AT&T
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=grbtkzijgrowvobvessf
gunzip temp.gz
mv temp isp-att.p2p

# (General) Corporations [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=gyisgnzbhppbvsphucsw
gunzip temp.gz
mv temp general-corporations-bluetack.p2p

# (ISP) Sprint
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=hngtqrhhuadlceqxbrob
gunzip temp.gz
mv temp isp-sprint.p2p

# (ISP) Charter
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=htnzojgossawhpkbulqw
gunzip temp.gz
mv temp isp-charter.p2p

# (ISP) Qwest
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=jezlifrpefawuoawnfez
gunzip temp.gz
mv temp isp-qwest.p2p

# (General) For Non-LAN Computers [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=jhaoawihmfxgnvmaqffp
gunzip temp.gz
mv temp general-for-non-lan-computers.p2p

# (General) Unallocated Address Space [CIDR]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=lujdnbasfaaixitgmxpp
gunzip temp.gz
mv temp general-unallocated-address-space-cidr.p2p

# (General) Spiders/Web Crawlers [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=mcvxsnihddgutbjfbghy
gunzip temp.gz
mv temp general-spiders-web-crawlers-bluetack.p2p

# (General) Exclusions [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=mtxmiireqmjzazcsoiem
gunzip temp.gz
mv temp general-exclusions.p2p

# (Organization) NCsoft
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=mwjuwmebrnzyyxpbezxu
gunzip temp.gz
mv temp organization-ncsoft.p2p

# (ISP) Cox Communications
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=nlgdvmvfxvoimdunmuju
gunzip temp.gz
mv temp isp-cox-communications.p2p

# (General) Malicious [CI Army]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=npkuuhuxcsllnhoamkvm
gunzip temp.gz
mv temp general-malicious-ci-army.p2p

# (Organization) The Pirate Bay
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=nzldzlpkgrcncdomnttb
gunzip temp.gz
mv temp organization-the-pirate-bay.p2p

# (Organization) Square Enix
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=odyaqontcydnodrlyina
gunzip temp.gz
mv temp organization-square-enix.p2p

# (General) Malware [malc0de]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=pbqcylkejciyhmwttify
gunzip temp.gz
mv temp general-malware-malc0de.p2p

# (Organization) Nintendo
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=pevkykuhgaegqyayzbnr
gunzip temp.gz
mv temp organization-nintendo.p2p

# (General) Suspicious/Under Investigation [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=plkehquoahljmyxjixpu
gunzip temp.gz
mv temp general-suspicious-under-investigation.p2p

# (Organization) Xfire
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ppqqnyihmcrryraaqsjo
gunzip temp.gz
mv temp organization-xfire.p2p

# (ISP) Suddenlink
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=psaoblrwylfrdsspfuiq
gunzip temp.gz
mv temp isp-suddenlink.p2p

# (General) IANA Multicast [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=pwqnlynprfgtjbgqoizj
gunzip temp.gz
mv temp general-iana-multicast.p2p

# (Organization) Linden Lab
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=qnjdimxnaupjmpqolxcv
gunzip temp.gz
mv temp organization-linden-lab.p2p

# (ISP) Comcast
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=rsgyxvuklicibautguia
gunzip temp.gz
mv temp isp-comcast.p2p

# (Organization) Riot Games
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=sdlvfabdjvrdttfjotcy
gunzip temp.gz
mv temp organization-riot-games.p2p

# (Organization) LogMeIn
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=tgbankumtwtrzllndbmb
gunzip temp.gz
mv temp organization-logmein.p2p

# (ISP) AOL
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=toboaiysofkflwgrttmb
gunzip temp.gz
mv temp isp-aol.p2p

# (Organization) The Onion Router
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=togdoptykrlolpddwbvz
gunzip temp.gz
mv temp organization-the-onion-router.p2p

# (Organization) Sony Entertainment
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=tukpvrvlubsputmkmiwg
gunzip temp.gz
mv temp organization-sony-entertainment.p2p

# (ISP) Embarq
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=twdblifaysaqtypevvdp
gunzip temp.gz
mv temp isp-embarq.p2p

# (Country) United States
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=us
gunzip temp.gz
mv temp country-united-states.p2p

# (General) Hijacked [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=usrcshglbiilevmyfhse
gunzip temp.gz
mv temp general-hijacked-bluetack.p2p

# (General) "Paranoid" List [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=uwnukjqktoggdknzrhgh
gunzip temp.gz
mv temp general-paranoid-list.p2p

# (General) Open Proxies [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=xoebmbyexwuiogmbyprb
gunzip temp.gz
mv temp general-open-proxies.p2p

# (General) Hackers [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=xpbqleszmajjesnzddhv
gunzip temp.gz
mv temp general-hackers.p2p

# (Organization) Microsoft [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=xshktygkujudfnjfioro
gunzip temp.gz
mv temp organization-microsoft.p2p

# (General) Possibly Abused Addresses [ZeuS]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=ynkdjqsjyfmilsgbogqf
gunzip temp.gz
mv temp general-possibly-abused-addresses.p2p

# (General) Botnets [Spamhaus]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=zbdlwrqkabxbcppvrnos
gunzip temp.gz
mv temp general-botnets.p2p

# (General) Advertising [Yoyo]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=zhogegszwduurnvsyhdf
gunzip temp.gz
mv temp general-advertising.p2p

# (General) SpyEye [Abuse]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=zhogegszwduurnvsyhdf
gunzip temp.gz
mv temp general-spyeye.p2p

# (Organization) PunkBuster
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=zvwwndvzulqcltsicwdg
gunzip temp.gz
mv temp organization-punkbuster.p2p

# (General) Malicious [Atma]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/atma/atma
gunzip temp.gz
mv temp general-malicious-atma.p2p

# (General) Fake File Shares [DCHubAd]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/dchubad/faker
gunzip temp.gz
mv temp general-fake-file-shares.p2p

# (General) Known Hackers [DCHubAd]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/dchubad/hacker
gunzip temp.gz
mv temp general-known-hackers.p2p

# (General) Bad Porn [DCHubAd]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/dchubad/pedophiles
gunzip temp.gz
mv temp general-bad-porn-dchubad.p2p

# (General) Spammers [DCHubAd]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/dchubad/spammer
gunzip temp.gz
mv temp general-spammers.p2p

# (General) Government/Anti-P2P ISPs [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/business-isps
gunzip temp.gz
mv temp general-government-anti-p2p-isps.p2p

# (General) Education [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/educational-institutions
gunzip temp.gz
mv temp general-education.p2p

# (General) Corporations [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/general-corporate-ranges
gunzip temp.gz
mv temp general-corporations-tbg.p2p

# (General) Hijacked Addresses [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/hijacked
gunzip temp.gz
mv temp general-hijacked-tbg.p2p

# (General) Primary Threats [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/primary-threats
gunzip temp.gz
mv temp general-primary-threats.p2p

# (General) Spiders/Web Crawlers [TBG]
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/tbg/search-engines
gunzip temp.gz
mv temp general-spiders-web-crawlers-tbg.p2p

# (Default) Ads List
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/bluetack/ads-trackers-and-bad-pr0n
gunzip temp.gz
mv temp default-ads-list.p2p

# (Default) P2P List
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/bluetack/level-1
gunzip temp.gz
mv temp default-p2p-list.p2p

# (Default) Spyware List
curl -A "" -L -o temp.gz http://list.iblocklist.com/lists/bluetack/spyware
gunzip temp.gz
mv temp default-spyware-list.p2p

# (General) Malware [Bluetack]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=qlprgwgdkojunfdlzsiv
gunzip temp.gz
mv temp general-malware-bluetack.p2p

# (General) Malware [MalwareDomainList]
curl -A "" -L -o temp.gz http://list.iblocklist.com/?list=cgbdjfsybgpgyjpqhsnd
gunzip temp.gz
mv temp general-malware-malwaredomainlist.p2p
