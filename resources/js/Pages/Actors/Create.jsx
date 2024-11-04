import React from 'react';
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import TextInput from '@/Components/TextInput';
import LoadingButton from '@/Components/LoadingButton';

export default function Create() {
    const { data, setData, post, errors, processing } = useForm({
        first_name: '',
        last_name: '',
        phone_number: '',
        date_of_birth: '2000-01-01',
        passport: '', // Додано поле passport
    });

    const handleChange = (e) => {
        const key = e.target.name;
        const value = e.target.value;
        setData(key, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('actors.store'));
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">
                <form onSubmit={handleSubmit}>
                    <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="First Name"
                            name="first_name"
                            errors={errors.first_name}
                            value={data.first_name}
                            onChange={handleChange}
                        />
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Last Name"
                            name="last_name"
                            errors={errors.last_name}
                            value={data.last_name}
                            onChange={handleChange}
                        />
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Phone Number"
                            name="phone_number"
                            type="text"
                            errors={errors.phone_number}
                            value={data.phone_number}
                            onChange={handleChange}
                        />
                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Passport"
                            name="passport"
                            type="text"
                            errors={errors.passport}
                            value={data.passport}
                            onChange={handleChange}
                        />
                        <div className="pr-6 pb-8 w-full lg:w-1/2">
                            <label className="block text-sm font-medium text-gray-700" htmlFor="date_of_birth">
                                Date of Birth
                            </label>
                            <input
                                id="date_of_birth"
                                name="date_of_birth"
                                type="date"
                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value={data.date_of_birth}
                                onChange={handleChange}
                            />
                            {errors.date_of_birth && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.date_of_birth}
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="btn-indigo ml-auto"
                        >
                            Create Actor
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
