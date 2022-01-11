#!/bin/bash

prompt () {                        # $1=question $2=options
    # set REPLY
    # options: x=..|y=..
    while $(true); do
        printf '%s [%s] ' "$1" "$2"
        stty cbreak
        REPLY=$(dd if=/dev/tty bs=1 count=1 2> /dev/null)
        stty -cbreak
        test "$REPLY" != "$(printf '\n')" && printf '\n'
        (
            IFS='|'
            for o in $2; do
                if [ "$REPLY" = "${o%%=*}" ]; then
                    printf '\n'
                    break
                fi
            done
        ) | grep ^ > /dev/null && return
    done
}

editFile() {
  prompt "Would you like to edit ${1} straight away?" "y=yes|n=no"
  if [ "$REPLY" = "y" ]; then
    if [ "$EDITOR" = "" ]; then
        $EDITOR ${1}
    else
        vim ${1}
    fi
  fi
}