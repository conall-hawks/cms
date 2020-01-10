<article>
    <h1>Compile Commands</h1>
    <code class="hljs dos">C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc.exe example.cs
C:\Windows\Microsoft.NET\Framework\v4.0.30319\msbuild.exe example.sln
C:\Windows\Microsoft.NET\Framework\v4.0.30319\msbuild.exe example.csproj
</code>
    <script nonce="<?php echo $security->nonce(); ?>">
        var elements = document.querySelectorAll("code");
        if(typeof window.hljs === "object" && window.hljsLanguagesLoading < 1){
            for(var i = elements.length - 1; i >= 0; i--){
                window.hljs.highlightBlock(elements[i]);
            }
        }
    </script>
</article>
