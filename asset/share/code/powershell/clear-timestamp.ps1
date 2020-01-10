################################################################################
# Clears all the timestamps in a file or folder.                               #
################################################################################

# Get all files/folders recursively.
function GetFiles($path = "."){
    foreach($node in Get-ChildItem $path -force){
        ClearTimestamps $node.FullName
        if(Test-Path $node.FullName -PathType Container){
            GetFiles $node.FullName
        }
    }
}

# Clear the timestamp of a file/folder.
function ClearTimestamps($node){
    Try{
        if($(Get-Item $node -force).isReadOnly){
            Set-ItemProperty $node -name IsReadOnly -value $false
            $(Get-Item $node -force).creationtime   = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
            $(Get-Item $node -force).lastaccesstime = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
            $(Get-Item $node -force).lastwritetime  = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
            Set-ItemProperty $node -name IsReadOnly -value $true
        }else{
            $(Get-Item $node -force).creationtime   = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
            $(Get-Item $node -force).lastaccesstime = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
            $(Get-Item $node -force).lastwritetime  = $((Get-Date "01/01/1980 00:00 AM").ToUniversalTime())
        }
        Write-Output "Cleared timestamp of: $($node)"
    }Catch{
        Write-Output "Unable to clear timestamp of: $($node)"
    }
}

# Clear the timestamp of a file/folder and its children.
ClearTimestamps $args[0]
GetFiles $args[0]
