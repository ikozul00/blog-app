export const formatDate = (dateString, lang) => {
    const date = new Date(dateString);
    return date.toLocaleString(lang);
}