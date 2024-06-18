export const capitalizeFirstLetter = (string: string|undefined): string|null => {
    if (string !== undefined) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    return null;
}