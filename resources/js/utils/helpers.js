export function getImageUrl(imageName) {
    return new URL(`${imageName}`, import.meta.url).href;
}
