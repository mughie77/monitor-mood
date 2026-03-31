<!-- Main content ends here -->
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!-- /#wrapper -->

<!-- Chart.js for graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS for Admin -->
<script>
    // Sidebar toggle functionality
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        const sidebar = document.getElementById("sidebar-wrapper");
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
        } else {
            sidebar.classList.add('hidden');
        }
    });
</script>

<?php if (isset($custom_js)): ?>
    <script src="<?php echo base_url($custom_js); ?>"></script>
<?php endif; ?>

</body>
</html>
