import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const ServicesPage = () => {
  const { t } = useTranslation();

  const services = [
    {
      id: 1,
      title: t('home.features.personalizedCoaching.title'),
      description: t('home.features.personalizedCoaching.description'),
      longDescription: t('servicesPage.personalizedCoaching.details'),
      icon: '👨‍🏫',
      features: [
        t('servicesPage.personalizedCoaching.feature1'),
        t('servicesPage.personalizedCoaching.feature2'),
        t('servicesPage.personalizedCoaching.feature3'),
        t('servicesPage.personalizedCoaching.feature4'),
      ],
      cta: t('servicesPage.findCoach'),
      ctaLink: '/coaches',
    },
    {
      id: 2,
      title: t('home.features.trackProgress.title'),
      description: t('home.features.trackProgress.description'),
      longDescription: t('servicesPage.trackProgress.details'),
      icon: '📊',
      features: [
        t('servicesPage.trackProgress.feature1'),
        t('servicesPage.trackProgress.feature2'),
        t('servicesPage.trackProgress.feature3'),
        t('servicesPage.trackProgress.feature4'),
      ],
      cta: t('servicesPage.getStarted'),
      ctaLink: '/',
    },
    {
      id: 3,
      title: t('home.features.flexibleBooking.title'),
      description: t('home.features.flexibleBooking.description'),
      longDescription: t('servicesPage.flexibleBooking.details'),
      icon: '📅',
      features: [
        t('servicesPage.flexibleBooking.feature1'),
        t('servicesPage.flexibleBooking.feature2'),
        t('servicesPage.flexibleBooking.feature3'),
        t('servicesPage.flexibleBooking.feature4'),
      ],
      cta: t('servicesPage.bookNow'),
      ctaLink: '/coaches',
    },
    {
      id: 4,
      title: t('home.features.community.title'),
      description: t('home.features.community.description'),
      longDescription: t('servicesPage.community.details'),
      icon: '👥',
      features: [
        t('servicesPage.community.feature1'),
        t('servicesPage.community.feature2'),
        t('servicesPage.community.feature3'),
        t('servicesPage.community.feature4'),
      ],
      cta: t('servicesPage.joinForum'),
      ctaLink: '/forum',
    },
  ];

  return (
    <div className="min-h-screen bg-gray-50 pt-24 pb-16">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="text-center mb-16">
          <h1 className="text-5xl font-bold text-gray-900 mb-4">
            {t('servicesPage.title')}
          </h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            {t('servicesPage.subtitle')}
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid md:grid-cols-2 gap-8 mb-12">
          {services.map((service, index) => (
            <div
              key={service.id}
              className={`bg-white rounded-xl shadow-lg p-8 border-2 border-gray-200 hover:border-blue-500 hover:shadow-xl transition-all ${
                index % 2 === 0 ? 'md:mt-0' : 'md:mt-12'
              }`}
            >
              <div className="flex items-start gap-6">
                <div className="flex-shrink-0">
                  <div className="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center text-5xl shadow-lg">
                    {service.icon}
                  </div>
                </div>
                <div className="flex-1">
                  <h2 className="text-2xl font-bold text-gray-900 mb-3">
                    {service.title}
                  </h2>
                  <p className="text-gray-600 mb-4 leading-relaxed">
                    {service.longDescription}
                  </p>
                  
                  <ul className="space-y-2 mb-6">
                    {service.features.map((feature, idx) => (
                      <li key={idx} className="flex items-start gap-2">
                        <svg className="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                        </svg>
                        <span className="text-gray-700">{feature}</span>
                      </li>
                    ))}
                  </ul>

                  <Link
                    to={service.ctaLink}
                    className="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition-all shadow-md"
                  >
                    {service.cta} →
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Additional Info Section */}
        <div className="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-12 text-white text-center shadow-xl">
          <h2 className="text-3xl font-bold mb-4">
            {t('servicesPage.cta.title')}
          </h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            {t('servicesPage.cta.subtitle')}
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              to="/coaches"
              className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all shadow-lg"
            >
              {t('servicesPage.cta.findCoach')}
            </Link>
            <Link
              to="/forum"
              className="bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all border-2 border-white/20"
            >
              {t('servicesPage.cta.joinCommunity')}
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ServicesPage;

