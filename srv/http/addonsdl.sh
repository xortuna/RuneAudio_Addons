#!/bin/bash

# existing 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )
installurl=$( echo "$addonslist" | grep 'installurl.*=>' | cut -d "'" -f 4 )
gitpath=$( dirname $installurl )
branch=''

# for testing branch: $1=branchname
if (( $# != 0 )); then
	branch=$1
	gitpath=$( echo $( dirname $gitpath )/$branch ) # switch 'master' to 'branch'
	installurl=$gitpath/install.sh
fi

dl=$( wget -qN $gitpath/srv/http/addonslist.php -P /srv/http )
result=$?
if [[ $result == 5 ]]; then # github 'certificate error' code
	curl -s -v -X POST 'http://localhost/pub?id=addons' -d 2
	
	systemctl stop ntpd
	ntpdate pool.ntp.org
	systemctl start ntpd
	
	echo "$dl" || exit 1
elif [[ $result != 0 ]]; then
	exit 1
fi

# new 'addonslist.php'
addonslist=$( sed -n "/'addo'/,/^),/p" /srv/http/addonslist.php )

versionlist=$( echo "$addonslist" | grep 'version.*=>' | cut -d "'" -f 4 )
versionredis=$( redis-cli hget addons addo )

if [[ $versionlist != $versionredis ]]; then
	wget -qN $installurl -P /srv/http
	[[ $? != 0 ]] && exit 1
	chmod 755 /srv/http/install.sh
	
	curl -s -v -X POST 'http://localhost/pub?id=addons' -d 1
	
	/usr/local/bin/uninstall_addo.sh
	/srv/http/install.sh $branch
fi

exit 0 # force exit code = 0
