<article class="content-box css-units">
	<h2>CSS Units</h2>
	<h3>Relative Lengths</h3>
	<table>
		<tr><td>%</td><td>Percentage of the parent element's width.</td></tr>
		<tr><td>ch</td><td>Width of the "0" (ZERO, U+0030) glyph in the element's font.</td></tr>
		<tr><td>em</td><td>Font size of the element. Font size of parent if used in a "font-size" property.</td><tr>
		<tr><td>ex</td><td>X-height of the element's font.</td><tr>
		<tr><td>rem</td><td>Font size of the root element.</td></tr>
		<tr><td>vmax</td><td>Percentage of the viewport's larger dimension.</td></tr>
		<tr><td>vmin</td><td>Percentage of the viewport's smaller dimension.</td></tr>
		<tr><td>vh</td><td>Percentage of the viewport's height.</td></tr>
		<tr><td>vw</td><td>Percentage of the viewport's width.</td></tr>
	</table>
	<p>Reference: <a href="https://www.w3.org/TR/css3-values/#relative-lengths">https://www.w3.org/TR/css3-values/#relative-lengths</a></p>
	
	<h3>Absolute Lengths</h3>
	<table>
		<tr><td>cm</td><td>Centimeters</td><td>1cm = 96px/2.54</td></tr>
		<tr><td>mm</td><td>Millimeters</td><td>1mm = 1/10th of 1cm</td></tr>
		<tr><td>q</td><td>Millimeters</td><td>1q = 1/40th of 1cm</td></tr>
		<tr><td>in</td><td>Inches</td><td>1in = 2.54cm = 96px</td><tr>
		<tr><td>pc</td><td>Picas</td><td>1pc = 1/6th of 1in</td></tr>
		<tr><td>pt</td><td>Points</td><td>1pt = 1/72th of 1in</td></tr>
		<tr><td>px*</td><td>Pixels</td><td>1px = 1/96th of 1in</td><tr>
	</table>
	<p>Reference: <a href="https://www.w3.org/TR/css3-values/#absolute-lengths">https://www.w3.org/TR/css3-values/#absolute-lengths</a></p>
	
	<h3>Calculated Values</h3>
	<table>
		<tr><td>Unit:</td><td>Description:</td><td>Example:</td></tr>
		<tr>
			<td>attr()</td>
			<td>Attribute Reference: Returns the value of an attribute on the element.</td>
			<td>
				<code class="css">div{ width: attr('myAttr px'); }</code> and <br />
				<code class="html">&lt;div myAttr="200"&gt;I'm 200px wide!&lt;/div&gt;</code>
			</td>
		</tr>
		<tr>
			<td>calc()</td>
			<td>Mathematical Expression: &lt;3 SIMPLY AMAZING! &lt;3</td>
			<td>
				<code class="css">width: calc(100% - 8px)"</code> and <br />
				<code class="html">&lt;div&gt;I'm 100% - 8 pixels wide!&lt;/div&gt;</code>
			</td>
		</tr>
		<tr>
			<td>toggle()</td>
			<td>Toggled Values: "font-style: toggle(italic, normal);"</td>
			<td>Would make text italic by default and normal if inside something that's italicized. Toggles between values: details <a href="https://www.w3.org/TR/css3-values/#toggle-notation">here</a>.</td></tr>
	</table>
	<p>Reference: <br />
		<a href="https://www.w3.org/TR/css3-values/#attr-notation">https://www.w3.org/TR/css3-values/#attr-notation</a><br />
		<a href="https://www.w3.org/TR/css3-values/#calc-notation">https://www.w3.org/TR/css3-values/#calc-notation</a><br />
		<a href="https://www.w3.org/TR/css3-values/#toggle-notation">https://www.w3.org/TR/css3-values/#toggle-notation</a><br />
	
	</p>
</article>
<style>
	.css-units td:first-of-type { width: 10%; }
	.css-units td { border: 1px solid rgba(255, 255, 255, .03125); padding: 2px 4px; }
</style>