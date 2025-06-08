<footer class="admin-footer">
                <p>&copy; <?= date('Y') ?> NSCT Admin Panel. All rights reserved.</p>
            </footer>
        </div><!-- /.admin-wrapper -->
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/admin/assets/js/main.js"></script>
        <?php if (isset($extraJS)): ?>
            <?php foreach ($extraJS as $js): ?>
                <script src="<?= $js ?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
</html>
