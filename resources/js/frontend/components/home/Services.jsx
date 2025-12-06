import React from 'react';
import { useTranslation } from 'react-i18next';

const Services = () => {
  const { t } = useTranslation();

  const services = [
    {
      title: t('home.features.personalizedCoaching.title'),
      description: t('home.features.personalizedCoaching.description'),
      icon: '👨‍🏫',
    },
    {
      title: t('home.features.trackProgress.title'),
      description: t('home.features.trackProgress.description'),
      icon: '📊',
    },
    {
      title: t('home.features.flexibleBooking.title'),
      description: t('home.features.flexibleBooking.description'),
      icon: '📅',
    },
    {
      title: t('home.features.community.title'),
      description: t('home.features.community.description'),
      icon: '👥',
    },
  ];

  return (
    <section id="services" className="py-20 bg-white">
      <div className="container mx-auto px-6">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              Our Services
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto mb-6">
              Everything you need to reach your fitness goals
            </p>
            <a
              href="/services"
              className="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition-all shadow-md"
            >
              Learn More About Our Services →
            </a>
          </div>

          <div className="grid md:grid-cols-3 gap-8 items-center">
            {/* Left Column - Services 1 & 2 */}
            <div className="space-y-6">
              {services.slice(0, 2).map((service, index) => (
                <div
                  key={index}
                  className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all"
                >
                  <div className="text-4xl mb-4">{service.icon}</div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    {service.title}
                  </h3>
                  <p className="text-gray-600">
                    {service.description}
                  </p>
                </div>
              ))}
            </div>

            {/* Center Column - Model Image */}
            <div className="relative">
              <div className="relative bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-8 border border-gray-200 shadow-xl">
                <div className="aspect-[3/4] bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center overflow-hidden">
                  <div className="text-center">
                    <div className="w-32 h-32 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                      <svg className="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                    <p className="text-gray-500 text-sm font-medium">Model Image</p>
                  </div>
                </div>
              </div>
              {/* Decorative Elements */}
              <div className="absolute -top-6 -right-6 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
              <div className="absolute -bottom-6 -left-6 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
            </div>

            {/* Right Column - Services 3 & 4 */}
            <div className="space-y-6">
              {services.slice(2, 4).map((service, index) => (
                <div
                  key={index}
                  className="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all"
                >
                  <div className="text-4xl mb-4">{service.icon}</div>
                  <h3 className="text-xl font-bold text-gray-900 mb-2">
                    {service.title}
                  </h3>
                  <p className="text-gray-600">
                    {service.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Services;

