</div></div>

<br><br>

<script>
    let tooltipTriggerList = [...[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')), ...[].slice.call(document.querySelectorAll('[data-bs-toggle2="tooltip"]'))]
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    function copy(text, url) {
        if (url) {
            navigator.clipboard.writeText("https://pone.eu.org/" + text);
        } else {
            navigator.clipboard.writeText(text);
        }
    }
</script>

</body>
</html>