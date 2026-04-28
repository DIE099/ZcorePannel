// Global helper functions
function copyToClipboard(text){
    navigator.clipboard.writeText(text);
    alert('Copied: '+text);
}
function confirmAction(msg){ return confirm(msg); }