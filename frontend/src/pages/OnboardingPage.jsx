import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../context/AuthContext';

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api';

export default function OnboardingPage() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const [step, setStep] = useState(1);
  const [options, setOptions] = useState({ spiritual_goals: [], interests: [], talents: [], daily_goals: [] });
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  // Form State
  const [formData, setFormData] = useState({
    date_of_birth: '',
    country: '',
    spiritual_goals: [],
    preferred_hour: '12:00 AM',
    custom_time: '',
    interests: [],
    talents: [],
    daily_goals: []
  });

  useEffect(() => {
    const fetchOptions = async () => {
      try {
        const res = await axios.get(`${API_BASE}/onboarding/options`);
        setOptions(res.data.data);
      } catch (err) {
        console.error('Failed to fetch onboarding options:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchOptions();
  }, []);

  const handleToggleSelect = (field, id) => {
    setFormData(prev => {
      const current = prev[field];
      const updated = current.includes(id) ? current.filter(item => item !== id) : [...current, id];
      return { ...prev, [field]: updated };
    });
  };

  const handleComplete = async () => {
    setSubmitting(true);
    try {
      await axios.post(`${API_BASE}/onboarding/complete`, formData);
      window.location.href = '/home';
    } catch (err) {
      alert('Failed to complete onboarding. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-midnight text-white flex items-center justify-center font-poppins">
        Loading options...
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-midnight text-slate-100 flex flex-col justify-between p-6 max-w-xl mx-auto font-poppins">
      
      {/* Header & Progress Indicator */}
      <div>
        <div className="flex justify-between items-center mb-6">
          <span className="text-softGold font-semibold text-lg">Self Review</span>
          <button onClick={logout} className="text-xs text-slate-400 hover:text-white">Sign Out</button>
        </div>

        {step > 1 && (
          <div className="w-full bg-slate-800 h-2 rounded-full mb-8">
            <div 
              className="bg-royalBlue h-2 rounded-full transition-all duration-300" 
              style={{ width: `${(step / 8) * 100}%` }}
            ></div>
          </div>
        )}

        {/* STEP 1: WELCOME */}
        {step === 1 && (
          <div className="text-center py-12">
            <h1 className="text-3xl font-bold mb-4">Welcome, {user?.first_name}!</h1>
            <p className="text-slate-300 text-lg mb-8">"One hour with God. One year of transformation."</p>
            <button 
              onClick={() => setStep(2)} 
              className="w-full py-4 bg-royalBlue hover:bg-blue-600 rounded-xl font-bold text-lg transition duration-200"
            >
              Get Started
            </button>
          </div>
        )}

        {/* STEP 2: PROFILE */}
        {step === 2 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Personal Details</h2>
            <p className="text-slate-400 text-sm mb-6">Tell us a little bit about yourself.</p>
            <div className="space-y-4">
              <div>
                <label className="block text-xs uppercase font-medium mb-1 text-slate-300">Date of Birth</label>
                <input 
                  type="date" 
                  value={formData.date_of_birth} 
                  onChange={e => setFormData({...formData, date_of_birth: e.target.value})} 
                  className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue" 
                />
              </div>
              <div>
                <label className="block text-xs uppercase font-medium mb-1 text-slate-300">Country</label>
                <input 
                  type="text" 
                  placeholder="e.g. Nigeria" 
                  value={formData.country} 
                  onChange={e => setFormData({...formData, country: e.target.value})} 
                  className="w-full p-3 rounded-xl bg-slate-800 text-white border border-slate-700 focus:outline-none focus:border-royalBlue" 
                />
              </div>
            </div>
          </div>
        )}

        {/* STEP 3: SPIRITUAL GOALS */}
        {step === 3 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Spiritual Goals</h2>
            <p className="text-slate-400 text-sm mb-6">Where do you want to experience spiritual growth?</p>
            <div className="grid grid-cols-2 gap-3">
              {options.spiritual_goals.map(goal => (
                <button
                  key={goal.id}
                  onClick={() => handleToggleSelect('spiritual_goals', goal.id)}
                  className={`p-3 rounded-xl border text-left text-sm font-medium transition ${
                    formData.spiritual_goals.includes(goal.id) ? 'bg-royalBlue border-royalBlue text-white' : 'bg-slate-800 border-slate-700 text-slate-300'
                  }`}
                >
                  {goal.name}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* STEP 4: PREFERRED HOUR */}
        {step === 4 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Preferred SR Hour</h2>
            <p className="text-slate-400 text-sm mb-6">Select your designated daily hour for communion.</p>
            <div className="grid grid-cols-2 gap-3 mb-4">
              {['11:00 PM', '12:00 AM', '1:00 AM', '2:00 AM', '3:00 AM'].map(time => (
                <button
                  key={time}
                  onClick={() => setFormData({...formData, preferred_hour: time})}
                  className={`p-3 rounded-xl border text-center font-medium transition ${
                    formData.preferred_hour === time ? 'bg-royalBlue border-royalBlue text-white' : 'bg-slate-800 border-slate-700 text-slate-300'
                  }`}
                >
                  {time}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* STEP 5: INTERESTS */}
        {step === 5 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Interests</h2>
            <p className="text-slate-400 text-sm mb-6">Select topics you are passionate about.</p>
            <div className="grid grid-cols-2 gap-3">
              {options.interests.map(item => (
                <button
                  key={item.id}
                  onClick={() => handleToggleSelect('interests', item.id)}
                  className={`p-3 rounded-xl border text-left text-sm font-medium transition ${
                    formData.interests.includes(item.id) ? 'bg-royalBlue border-royalBlue text-white' : 'bg-slate-800 border-slate-700 text-slate-300'
                  }`}
                >
                  {item.name}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* STEP 6: TALENTS */}
        {step === 6 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Talents & Skills</h2>
            <p className="text-slate-400 text-sm mb-6">Select your primary skills and abilities.</p>
            <div className="grid grid-cols-2 gap-3">
              {options.talents.map(item => (
                <button
                  key={item.id}
                  onClick={() => handleToggleSelect('talents', item.id)}
                  className={`p-3 rounded-xl border text-left text-sm font-medium transition ${
                    formData.talents.includes(item.id) ? 'bg-royalBlue border-royalBlue text-white' : 'bg-slate-800 border-slate-700 text-slate-300'
                  }`}
                >
                  {item.name}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* STEP 7: DAILY GOALS */}
        {step === 7 && (
          <div>
            <h2 className="text-2xl font-bold mb-2">Daily Focus Habits</h2>
            <p className="text-slate-400 text-sm mb-6">Select habits you want to cultivate daily.</p>
            <div className="grid grid-cols-2 gap-3">
              {options.daily_goals.map(item => (
                <button
                  key={item.id}
                  onClick={() => handleToggleSelect('daily_goals', item.id)}
                  className={`p-3 rounded-xl border text-left text-sm font-medium transition ${
                    formData.daily_goals.includes(item.id) ? 'bg-royalBlue border-royalBlue text-white' : 'bg-slate-800 border-slate-700 text-slate-300'
                  }`}
                >
                  {item.name}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* STEP 8: SUMMARY & SUBMIT */}
        {step === 8 && (
          <div>
            <h2 className="text-2xl font-bold mb-4 text-softGold">Review Your Selections</h2>
            <div className="bg-slate-800 p-5 rounded-2xl space-y-3 text-sm border border-slate-700">
              <div><strong className="text-slate-400">Preferred Hour:</strong> {formData.preferred_hour}</div>
              <div><strong className="text-slate-400">Spiritual Goals:</strong> {formData.spiritual_goals.length} selected</div>
              <div><strong className="text-slate-400">Interests:</strong> {formData.interests.length} selected</div>
              <div><strong className="text-slate-400">Talents:</strong> {formData.talents.length} selected</div>
              <div><strong className="text-slate-400">Daily Goals:</strong> {formData.daily_goals.length} selected</div>
            </div>
          </div>
        )}
      </div>

      {/* Navigation Buttons */}
      {step > 1 && (
        <div className="flex gap-4 mt-8">
          <button 
            onClick={() => setStep(s => s - 1)} 
            className="w-1/2 py-3 bg-slate-800 hover:bg-slate-700 rounded-xl font-medium border border-slate-700"
          >
            Back
          </button>

          {step < 8 ? (
            <button 
              onClick={() => setStep(s => s + 1)} 
              className="w-1/2 py-3 bg-royalBlue hover:bg-blue-600 rounded-xl font-bold"
            >
              Next
            </button>
          ) : (
            <button 
              onClick={handleComplete} 
              disabled={submitting} 
              className="w-1/2 py-3 bg-softGold text-midnight font-bold rounded-xl hover:bg-yellow-400 transition"
            >
              {submitting ? 'Saving...' : 'Start My Journey'}
            </button>
          )}
        </div>
      )}
    </div>
  );
}