#!/bin/sh
################################################################################
# Installs the Sublime Text editor on Red Hat-based distributions.             #
################################################################################

# Download the archive.
curl -L https://download.sublimetext.com/sublime_text_3_build_3126_x64.tar.bz2 -o /usr/src/sublime-text-3.tar.bz2

# Extract the archive.
mkdir -p /usr/local/sublime-text-3
tar -xvjf /usr/src/sublime-text-3.tar.bz2 -C /usr/local/sublime-text-3 --strip-components=1

# Delete the archive.
rm -f /usr/src/sublime-text-3.tar.bz2

# Build the desktop entry file.
echo "[Desktop Entry]
Name=Sublime Text 3
Comment=Edit text files
Exec=/usr/local/sublime-text-3/sublime_text
Icon=/usr/local/sublime-text-3/Icon/128x128/sublime-text.png
Terminal=false
Type=Application
Encoding=UTF-8
Categories=Utility;TextEditor;
" > /usr/share/applications/sublime-text-3.desktop

# Build the shortcut file.
echo "#!/bin/sh
/usr/local/sublime-text-3/sublime_text \$@ > /dev/null 2>&1 &
" > /usr/local/bin/subl
chmod +x /usr/local/bin/subl

# Yay!
echo "Done; type \"subl\" to start."
