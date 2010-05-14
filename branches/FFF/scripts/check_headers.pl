#!/usr/bin/perl -w

use strict;
my $i;

opendir (WORKDIR, ".") || die "Can't open .: $!\n";
my @tmpfiles=grep(/report.php$/,readdir WORKDIR);
close(WORKDIR);
my @files=sort @tmpfiles;
my @rfiles=@files;

foreach my $file (@rfiles) {
    $file=~s/report.php/query/;
}

my $whead=0;
my $wrtp="";
my $wtitle="";
my $rtitle="";
my $wdesc="";
my $rdesc="";
my $wadd="";
my $wwants="";
my $rwants="";
my $rquery="";
my $woutfile="";
my $migrated=0;
my $rmigrated="";
my $dmigrated="";
for ($i=0;$i<=$#files;$i++) {

    # wipe everything
    $wrtp=$wtitle=$rtitle=$wdesc=$rdesc=$wadd=$wwants=$rwants=$woutfile=$rmigrated=$dmigrated="";
    $whead=7;
    $migrated=0;

    # Get the information from the files
    open (WEBFILE, "$files[$i]") || die "Can't open $files[$i]: $!\n";
    while (<WEBFILE>) {
	if (($_ =~ /^<?php$/) ||
	    ($_ =~ /^    require_once\('db_functions.php'\);$/) ||
	    ($_ =~ /^    require_once\('StaffHeader.php'\);$/) ||
	    ($_ =~ /^    require_once\('StaffFooter.php'\);$/) ||
	    ($_ =~ /^    require_once\('StaffCommonCode.php'\);$/) ||
	    ($_ =~ /^    global \$link;$/) ||
	    ($_ =~ /^    \$ConStartDatim=CON_START_DATIM; \/\/ make it a variable so it can be substituted$/) ||
	    ($_ =~ /^    ## LOCALIZATIONS/)) {$whead--;}
	if ($_ =~ /^    \$_SESSION\['return_to_page'\]="(.*)";$/) {$wrtp=$1;}
	if ($_ =~ /^    \$title="(.*)";$/) {$wtitle=$1;}
	if ($_ =~ /^    \$description="<P>(.*)<\/P>\\n";$/) {
	    $wdesc=$1;
	    $wdesc=~s/\\//g;
	}
	if ($_ =~ /^    \$additionalinfo="(.*)";$/) {
	    $wadd=$1;
	    if (!$wadd) {$wadd="empty";}
	}
	if ($_ =~ /^    \$indicies="(.*)";$/) {$wwants=$1;}
	if ($_ =~ /^    \$resultsfile="(.*)";$/) {$woutfile=$1;}
    }
    close (WEBFILE);
    open (QUERYFILE, "reportqueries/$rfiles[$i]") || die "Can't open $rfiles[$i]: $!\n";
    while (<QUERYFILE>) {
	if ($_ =~ /^TITLE=.(.*).$/) {$rtitle=$1;}
	if ($_ =~ /^DESCRIPTION=.(.*).$/) {$dmigrated=$1;}
	if ($_ =~ /^DESCRIPTION=.(.*) \[On&nbsp;Demand\]/) {$rdesc=$1}
	if (($_ =~ /WANTS/) && ($_ !~ /^TITLE/) && ($_ !~ /^DESCRIPTION/)) {
	    $rwants.=$_;
	    chomp $rwants;
	    $rwants.=", ";
	}
	if (($_ =~ /CSV/) && ($_ !~ /^TITLE/) && ($_ !~ /^DESCRIPTION/) && ($_ !~ /WANTS/)) {
	    $rwants.=$_;
	    chomp $rwants;
	    $rwants.=", ";
	}
	if ($_ =~ /^QUERY=/) {
	    $rquery=$_;
	    chomp $rquery;
	}
	if ($_ =~ /^MIGRATED/) {
	    $migrated++;
	    $rmigrated=$_;
	    chomp $rmigrated;
	}
    }
    close (QUERYFILE);
    chop $rwants;
    chop $rwants;

    # Compaire the information
    if ($migrated) {
        if ($rmigrated ne "MIGRATED=true") {
	    print "$rfiles[$i] bad migration line:\n$rmigrated\n";
        } elsif ($rquery ne "QUERY=' '") {
	    print "$rfiles[$i] bad query line:\n$rquery\n";
	    `emacs reportqueries/$rfiles[$i]`;
	} elsif ($whead) {
	    print "Messed up header lines $files[$i]:\n";
	    `emacs $files[$i]`;
	} elsif (!$wrtp) {
	    print "Missing/empty \$_SESSION\['return_to_page'\]='' $files[$i]:\n";
	    `emacs $files[$i]`;
        } elsif (($wrtp ne $files[$i]) and ($wrtp ne "\$scriptname")) {
	    print "Return To Page problem $files[$i]:\n $wrtp\n $files[$i]\n";
	    `emacs $files[$i]`;
	} elsif (!$wtitle) {
	    print "Missing/empty title $files[$i]:\n $rtitle\n";
	    `emacs reportqueries/$rfiles[$i] $files[$i]`;
	} elsif ($wtitle ne $rtitle) {
	    print "Title problem $files[$i]:\n $rtitle\n $wtitle\n";
	    `emacs reportqueries/$rfiles[$i] $files[$i]`;
	} elsif (!$rdesc) {
	    if ($wdesc ne $dmigrated) {
		print "Missing [On&nbsp;Demand] from $rfiles[$i] and migrated poorly:\n $wdesc\n $dmigrated\n $rdesc\n";
		`emacs $files[$i] reportqueries/$rfiles[$i]`;
	    } else {
		print "Missing [On&nbsp;Demand] from $rfiles[$i]:\n $dmigrated\n";
		`emacs reportqueries/$rfiles[$i]`;
	    }
	} elsif ($wdesc ne $rdesc) {
	    print "Description problem $files[$i]:\n $rdesc\n $wdesc\n";
	    `emacs reportqueries/$rfiles[$i] $files[$i]`;
	} elsif (!$wadd) {
	    print "Additional information missing/problem $files[$i]:\n";
	    `emacs $files[$i]`;
	} elsif (!$wwants) {
	    print "Missing indicies $files[$i]:\n $rwants\n";
	} elsif ($wwants ne $rwants) {
	    print "Indicies problem $files[$i]:\n $rwants\n $wwants\n";
	    `emacs reportqueries/$rfiles[$i] $files[$i]`;
	} elsif (($wwants =~ /csv/i) && !$woutfile) {
	    print "Missing CSV file name $files[$i]\n";
	    `emacs $files[$i]`;
	}
    }
}
