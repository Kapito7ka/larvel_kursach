import React from 'react';
import Icon from '@/Components/Icon';

export default function DateInput({ label, name, value, onChange, errors }) {
    return (
        <div className="mb-4">
            <label htmlFor={name} className="block text-sm font-medium text-gray-700">
                {label}
            </label>
            <input
                type="date"
                name={name}
                id={name}
                value={value}
                onChange={onChange}
                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
            {errors && <span className="text-red-600 text-sm">{errors}</span>}
        </div>
    );
}

