import React from 'react';
import { useTranslation } from 'react-i18next';

const Coaches = () => {
  const { t } = useTranslation();

  const coaches = [
    {
      name: 'John Smith',
      specialization: 'Strength Training',
      experience: '10+ years',
      image: '👨‍💼',
    },
    {
      name: 'Sarah Johnson',
      specialization: 'Yoga & Flexibility',
      experience: '8+ years',
      image: '👩‍💼',
    },
    {
      name: 'Mike Davis',
      specialization: 'Cardio Fitness',
      experience: '12+ years',
      image: '👨‍💼',
    },
    {
      name: 'Emily Wilson',
      specialization: 'Nutrition',
      experience: '7+ years',
      image: '👩‍💼',
    },
  ];

  return (
    <section id="coaches" className="py-20 bg-gray-50">
      <div className="container mx-auto px-6">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              Meet Our Coaches
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Expert trainers dedicated to helping you achieve your fitness goals
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {coaches.map((coach, index) => (
              <div
                key={index}
                className="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-blue-500 hover:shadow-xl transition-all transform hover:-translate-y-2 text-center"
              >
                <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                  {coach.image}
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {coach.name}
                </h3>
                <p className="text-blue-600 font-semibold mb-2">
                  {coach.specialization}
                </p>
                <p className="text-gray-500 text-sm mb-4">
                  {coach.experience} experience
                </p>
                <a
                  href="/register"
                  className="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold text-sm hover:from-blue-700 hover:to-purple-700 transition-all"
                >
                  Book Session
                </a>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Coaches;

