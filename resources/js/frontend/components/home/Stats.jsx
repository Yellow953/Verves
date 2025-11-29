import React from 'react';
import { useTranslation } from 'react-i18next';

const Stats = () => {
  const { t } = useTranslation();

  const stats = [
    { value: '10K+', label: t('home.stats.members') },
    { value: '500+', label: t('home.stats.coaches') },
    { value: '1K+', label: t('home.stats.programs') },
    { value: '50K+', label: t('home.stats.sessions') },
  ];

  return (
    <section className="py-16 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 text-white relative overflow-hidden">
      <div className="absolute inset-0 bg-black/10"></div>
      <div className="container mx-auto px-6 relative z-10">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
          {stats.map((stat, index) => (
            <div key={index} className="text-center">
              <div className="text-4xl md:text-6xl font-black mb-2 text-white">
                {stat.value}
              </div>
              <div className="text-lg md:text-xl text-blue-100 font-medium">
                {stat.label}
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Stats;

