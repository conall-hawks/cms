<article class="content-box">
	<h2>Under construction</h2>
	<p>Currently in alpha.<img src="/images/construction.jpg" title="That's a big rig." /></p>
	<p>
		In the meantime, here's some fun SQL Injection 101; login as anyone:<br />
		Username: &lt;Your enemy's username here&gt;<br />
		Password: <code class="sql">' OR 1=1/*</code><br />
		<br />
		That's: apostrophe, space, the-letter-O, the-letter-R, the-number-1, an 
		equals-sign, the-number-1, forward-slash, asterisk. In the password field. If 
		the server is vulnerable you can log in as anyone you want, provided you know 
		their username. However, not many servers are vulnerable nowadays.
	</p>
</article>