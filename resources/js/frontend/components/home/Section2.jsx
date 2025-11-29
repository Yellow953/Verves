import React from 'react';
import { useTranslation } from 'react-i18next';

const Section2 = () => {
  const { t } = useTranslation();

  return (
    <section className="py-20 bg-gray-50">
      <div className="container mx-auto px-6">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-2 gap-16 items-center">
            {/* Left Column - Image and Shapes */}
            <div className="relative order-2 md:order-1">
              {/* Decorative Shapes */}
              <div className="absolute -top-10 -left-10 w-72 h-72 bg-gradient-to-br from-purple-400/20 to-blue-400/20 rounded-full blur-3xl"></div>
              <div className="absolute -bottom-10 -right-10 w-64 h-64 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
              
              {/* Main Image Container */}
              <div className="relative bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 border border-gray-200 shadow-xl">
                <div className="aspect-square bg-gradient-to-br from-purple-100 to-blue-100 rounded-2xl flex items-center justify-center overflow-hidden">
                  <div className="text-center">
                    <div className="w-40 h-40 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                      <svg className="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <p className="text-gray-500 text-sm font-medium">Achievement Image</p>
                  </div>
                </div>
              </div>
              
              {/* Floating Elements */}
              <div className="absolute top-20 -right-8 w-16 h-16 bg-purple-500 rounded-2xl -rotate-12 opacity-20"></div>
              <div className="absolute bottom-20 -left-8 w-20 h-20 bg-blue-500 rounded-full opacity-20"></div>
            </div>

            {/* Right Column - Text Content */}
            <div className="order-1 md:order-2">
              <h2 className="text-4xl md:text-5xl font-bold mb-6 text-gray-900">
                Achieve Your Fitness Goals
              </h2>
              <p className="text-xl mb-6 text-gray-600 leading-relaxed">
                Our comprehensive platform provides everything you need to succeed in your fitness journey. From personalized training programs to progress tracking, we've got you covered.
              </p>
              <p className="text-lg mb-8 text-gray-600 leading-relaxed">
                Join thousands of members who have transformed their lives through our proven system. Start your journey today and see the results you've been dreaming of.
              </p>
              <a
                href="/register"
                className="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg"
              >
                Start Your Journey
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Section2;

