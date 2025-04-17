export const getEcuadorDate = () => {
    return new Date().toLocaleDateString('en-Ca', { timeZone: 'America/Guayaquil' });
}
