export const CLOTHING_MODES = {
    ISSUE: {
        hasRadio: true,
        hasExpiry: true,
        hasMinQuantity: false,
        hasCompany: false,
        initModules: ['ClothingCode', 'ClothingRowUI', 'ClothingSizesLoader']
    },
    ORDER: {
        hasRadio: false,
        hasExpiry: false,
        hasMinQuantity: true,
        hasCompany: true,
        initModules: ['ProductSuggestions', 'CheckClothing']
    }
};
