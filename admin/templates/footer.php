<!-- Main content ends here -->
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!-- /#wrapper -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js for graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS for Admin -->
<script>
    // Sidebar toggle functionality
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>

<?php if (isset($custom_js)): ?>
    <script src="<?php echo base_url($custom_js); ?>"></script>
<?php endif; ?>

</body>
</html>
