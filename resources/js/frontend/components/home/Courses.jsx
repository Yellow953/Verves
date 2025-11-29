import React from 'react';
import { useTranslation } from 'react-i18next';

const Courses = () => {
  const { t } = useTranslation();

  const courses = [
    {
      title: 'Strength Training',
      description: 'Build muscle and increase strength with our comprehensive program',
      duration: '12 weeks',
      level: 'Beginner',
    },
    {
      title: 'Cardio Fitness',
      description: 'Improve your cardiovascular health and endurance',
      duration: '8 weeks',
      level: 'Intermediate',
    },
    {
      title: 'Yoga & Flexibility',
      description: 'Enhance flexibility and find inner peace through yoga',
      duration: '10 weeks',
      level: 'All Levels',
    },
    {
      title: 'Nutrition Planning',
      description: 'Learn proper nutrition to fuel your fitness journey',
      duration: '6 weeks',
      level: 'All Levels',
    },
  ];

  return (
    <section id="courses" className="py-20 bg-gray-50">
      <div className="container mx-auto px-6">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              Our Courses
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Choose from a variety of programs designed to help you achieve your goals
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {courses.map((course, index) => (
              <div
                key={index}
                className="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-blue-500 hover:shadow-xl transition-all transform hover:-translate-y-2"
              >
                <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mb-4">
                  <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 19 7.5 19s3.332-.523 4.5-1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.523 4.5 1.253v13C19.832 18.477 18.247 19 16.5 19c-1.746 0-3.332-.523-4.5-1.253" />
                  </svg>
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {course.title}
                </h3>
                <p className="text-gray-600 mb-4">
                  {course.description}
                </p>
                <div className="flex items-center justify-between text-sm text-gray-500">
                  <span>{course.duration}</span>
                  <span className="bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium">
                    {course.level}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Courses;

