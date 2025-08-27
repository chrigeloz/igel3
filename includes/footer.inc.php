<footer>
    <p>Footer - Placeholder</p>
    <!-- Bug Report Form -->
<!-- Bug Report Form -->
<!-- Bug Report Form -->
    
<link rel="stylesheet" href="../styles.css">
<div id="bug-report" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;">
    <h4>Report bugs or suggestions</h4>
    <form method="POST" action="/includes/bug_report_handler.php">
        <input type="hidden" name="page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
        <textarea name="message" placeholder="Description" required></textarea><br><br>
        <button type="submit">Submit</button>
    </form>
</div>

</footer>
</body>
</html>