</main>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<?php if (isset($custom_js)): ?>
    <script src="<?php echo base_url($custom_js); ?>"></script>
<?php endif; ?>
</body>
</html>
