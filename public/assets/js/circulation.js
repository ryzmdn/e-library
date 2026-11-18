document.addEventListener('DOMContentLoaded', () => {
    const memberInput = document.getElementById('member_identifier');
    const copyOutInput = document.getElementById('copy_barcode_out');
    const copyInInput = document.getElementById('copy_barcode_in');

    if (memberInput && copyOutInput) {
        memberInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && memberInput.value.trim() !== '') {
                e.preventDefault();
                copyOutInput.focus();
            }
        });
    }

    if (copyInInput) {
        copyInInput.focus();
    }
});
