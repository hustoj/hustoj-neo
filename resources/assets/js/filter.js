export function showStatusBtn(status) {
    if (status == 1) {
        return 'Enable';
    }
    return 'Disable';
}

export function privateStatus(status) {
    if (status) {
        return 'Private';
    }
    return 'Public';
}
