import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { programsAPI, programExercisesAPI, exercisesAPI, relationshipsAPI } from '../../services/api';

const ProgramBuilder = () => {
  const { t } = useTranslation();
  const [programs, setPrograms] = useState([]);
  const [clients, setClients] = useState([]);
  const [exercises, setExercises] = useState([]);
  const [muscleGroups, setMuscleGroups] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [selectedProgram, setSelectedProgram] = useState(null);
  const [showExerciseLibrary, setShowExerciseLibrary] = useState(false);
  
  // Filters
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedMuscleGroup, setSelectedMuscleGroup] = useState('');
  
  // Form state
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    client_id: '',
    type: 'strength',
    duration_weeks: 4,
    goals: [],
  });

  useEffect(() => {
    loadData();
  }, []);

  useEffect(() => {
    if (showExerciseLibrary) {
      loadExercises();
    }
  }, [showExerciseLibrary, selectedMuscleGroup, searchTerm]);

  const loadData = async () => {
    try {
      setLoading(true);
      const [programsRes, clientsRes, muscleGroupsRes] = await Promise.all([
        programsAPI.list(),
        relationshipsAPI.list({ status: 'active' }),
        exercisesAPI.getMuscleGroups(),
      ]);
      setPrograms(programsRes.data.data.data || []);
      setClients(clientsRes.data.data.data || []);
      setMuscleGroups(muscleGroupsRes.data.data || []);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const loadExercises = async () => {
    try {
      const params = {};
      if (searchTerm) params.search = searchTerm;
      if (selectedMuscleGroup) params.muscle_group = selectedMuscleGroup;
      const response = await exercisesAPI.list(params);
      setExercises(response.data.data.data || []);
    } catch (err) {
      console.error('Failed to load exercises:', err);
    }
  };

  const handleCreateProgram = async (e) => {
    e.preventDefault();
    try {
      const relationship = clients.find(c => c.client.id === parseInt(formData.client_id));
      const programData = {
        ...formData,
        relationship_id: relationship?.id,
        goals: formData.goals.length > 0 ? formData.goals : null,
      };
      const response = await programsAPI.create(programData);
      if (response.data.success) {
        setShowCreateModal(false);
        setFormData({
          name: '',
          description: '',
          client_id: '',
          type: 'strength',
          duration_weeks: 4,
          goals: [],
        });
        loadData();
        setSelectedProgram(response.data.data);
      }
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to create program');
    }
  };

  const handleAddExercise = async (exercise, dayNumber = 1) => {
    if (!selectedProgram) return;
    try {
      const exerciseData = {
        exercise_name: exercise.name,
        description: exercise.description,
        muscle_group: exercise.muscle_group,
        equipment: exercise.equipment,
        day_number: dayNumber,
        sets: 3,
        reps: '10-12',
        instructions: exercise.instructions,
        video_urls: exercise.video_urls,
        images: exercise.images,
      };
      await programExercisesAPI.create(selectedProgram.id, exerciseData);
      loadProgramExercises(selectedProgram.id);
      setShowExerciseLibrary(false);
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to add exercise');
    }
  };

  const loadProgramExercises = async (programId) => {
    try {
      const response = await programExercisesAPI.list(programId);
      setSelectedProgram({
        ...selectedProgram,
        exercises: response.data.data || [],
      });
    } catch (err) {
      console.error('Failed to load program exercises:', err);
    }
  };

  if (loading) {
    return <div className="text-center py-8 text-gray-600">{t('common.loading')}</div>;
  }

  return (
    <div>
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 className="text-2xl font-bold text-gray-900">{t('coach.dashboard.myPrograms')}</h2>
        <button
          onClick={() => setShowCreateModal(true)}
          className="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all"
        >
          {t('program.createProgram')}
        </button>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
          {error}
        </div>
      )}

      {programs.length === 0 ? (
        <div className="text-center py-12">
          <p className="text-gray-500 mb-4">{t('coach.dashboard.noPrograms')}</p>
          <button
            onClick={() => setShowCreateModal(true)}
            className="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold"
          >
            {t('program.createProgram')}
          </button>
        </div>
      ) : (
        <div className="space-y-4">
          {programs.map((program) => (
            <div
              key={program.id}
              className="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow"
            >
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-1">{program.name}</h3>
                  <p className="text-sm text-gray-600 mb-2">{program.description}</p>
                  <div className="flex gap-4 text-sm text-gray-600">
                    <span>
                      <span className="font-medium">{t('coach.dashboard.client')}:</span>{' '}
                      {program.client?.name}
                    </span>
                    <span>
                      <span className="font-medium">{t('program.duration')}:</span>{' '}
                      {program.duration_weeks} {t('coach.dashboard.weeks')}
                    </span>
                    <span className={`px-3 py-1 rounded-full text-xs font-semibold ${
                      program.status === 'active' ? 'bg-green-100 text-green-800' :
                      program.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                      'bg-gray-100 text-gray-800'
                    }`}>
                      {t(`program.${program.status}`)}
                    </span>
                  </div>
                </div>
                <button
                  onClick={() => {
                    setSelectedProgram(program);
                    loadProgramExercises(program.id);
                  }}
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
                >
                  {t('coach.dashboard.edit')}
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Create Program Modal */}
      {showCreateModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-xl font-bold text-gray-900">{t('program.createProgram')}</h3>
              <button
                onClick={() => setShowCreateModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <form onSubmit={handleCreateProgram} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  {t('program.name')} *
                </label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  {t('program.description')}
                </label>
                <textarea
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  rows={3}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    {t('coach.dashboard.client')} *
                  </label>
                  <select
                    value={formData.client_id}
                    onChange={(e) => setFormData({ ...formData, client_id: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">{t('coach.dashboard.selectClient')}</option>
                    {clients.map((rel) => (
                      <option key={rel.id} value={rel.client.id}>
                        {rel.client.name}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    {t('program.duration')} ({t('coach.dashboard.weeks')})
                  </label>
                  <input
                    type="number"
                    value={formData.duration_weeks}
                    onChange={(e) => setFormData({ ...formData, duration_weeks: parseInt(e.target.value) })}
                    min="1"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>
              <div className="flex gap-3">
                <button
                  type="button"
                  onClick={() => setShowCreateModal(false)}
                  className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  {t('common.cancel')}
                </button>
                <button
                  type="submit"
                  className="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700"
                >
                  {t('common.create')}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Program Editor with Exercise Library */}
      {selectedProgram && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl shadow-xl max-w-6xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-xl font-bold text-gray-900">
                {t('coach.dashboard.editProgram')}: {selectedProgram.name}
              </h3>
              <button
                onClick={() => {
                  setSelectedProgram(null);
                  setShowExerciseLibrary(false);
                }}
                className="text-gray-400 hover:text-gray-600"
              >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            
            <div className="mb-4">
              <button
                onClick={() => setShowExerciseLibrary(!showExerciseLibrary)}
                className="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700"
              >
                {showExerciseLibrary ? t('coach.dashboard.hideLibrary') : t('coach.dashboard.showLibrary')}
              </button>
            </div>

            {showExerciseLibrary && (
              <div className="mb-6 p-4 bg-gray-50 rounded-lg">
                <div className="flex gap-4 mb-4">
                  <input
                    type="text"
                    placeholder={t('common.search')}
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                  />
                  <select
                    value={selectedMuscleGroup}
                    onChange={(e) => setSelectedMuscleGroup(e.target.value)}
                    className="px-4 py-2 border border-gray-300 rounded-lg"
                  >
                    <option value="">{t('coach.dashboard.allMuscleGroups')}</option>
                    {muscleGroups.map((mg) => (
                      <option key={mg} value={mg}>{mg}</option>
                    ))}
                  </select>
                </div>
                <div className="grid md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                  {exercises.map((exercise) => (
                    <div
                      key={exercise.id}
                      className="bg-white p-4 rounded-lg border border-gray-200 hover:shadow-md cursor-pointer"
                      onClick={() => handleAddExercise(exercise)}
                    >
                      <h4 className="font-semibold text-gray-900 mb-1">{exercise.name}</h4>
                      <p className="text-sm text-gray-600 mb-2">{exercise.muscle_group}</p>
                      <p className="text-xs text-gray-500 line-clamp-2">{exercise.description}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            <div>
              <h4 className="font-semibold text-gray-900 mb-4">{t('program.exercises')}</h4>
              {selectedProgram.exercises && selectedProgram.exercises.length > 0 ? (
                <div className="space-y-2">
                  {selectedProgram.exercises.map((exercise, index) => (
                    <div key={exercise.id} className="bg-gray-50 p-4 rounded-lg border border-gray-200">
                      <div className="flex justify-between items-start">
                        <div>
                          <h5 className="font-semibold text-gray-900">{exercise.exercise_name}</h5>
                          <p className="text-sm text-gray-600">
                            {t('coach.dashboard.day')} {exercise.day_number} • {exercise.sets} sets × {exercise.reps} reps
                          </p>
                        </div>
                        <button
                          onClick={async () => {
                            await programExercisesAPI.delete(selectedProgram.id, exercise.id);
                            loadProgramExercises(selectedProgram.id);
                          }}
                          className="text-red-600 hover:text-red-800"
                        >
                          {t('common.delete')}
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-gray-500 text-center py-8">
                  {t('coach.dashboard.noExercises')} {t('coach.dashboard.addExercises')}
                </p>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default ProgramBuilder;

