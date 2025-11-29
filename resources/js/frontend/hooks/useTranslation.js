/**
 * Custom hook wrapper for react-i18next useTranslation
 * Provides easier access to translation function
 */
import { useTranslation as useI18nTranslation } from 'react-i18next';

export const useTranslation = () => {
  const { t, i18n } = useI18nTranslation();
  
  return {
    t,
    i18n,
    currentLanguage: i18n.language,
    changeLanguage: (lng) => i18n.changeLanguage(lng),
    isEnglish: i18n.language === 'en' || i18n.language.startsWith('en'),
    isFrench: i18n.language === 'fr' || i18n.language.startsWith('fr'),
  };
};

export default useTranslation;

