import React, { useState } from 'react';
import Helmet from 'react-helmet';
import { Inertia } from '@inertiajs/inertia';
import { Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import DeleteButton from '@/Components/DeleteButton';
import LoadingButton from '@/Components/LoadingButton';
import TextInput from '@/Components/TextInput';
import SelectInput from '@/Components/SelectInput';
import TrashedMessage from '@/Components/TrashedMessage';

export default () => {
    const { show, performances, halls, errors } = usePage().props;

    const [sending, setSending] = useState(false);

    const [values, setValues] = useState({
        performance_id: show.performance_id || '',
        datetime: show.datetime || '',
        price: show.price || '',
        hall_id: show.hall_id || '',
    });

    function handleChange(e) {
        const key = e.target.name;
        const value = e.target.value;
        setValues(values => ({
            ...values,
            [key]: value
        }));
    }

    function handleSubmit(e) {
        e.preventDefault();
        setSending(true);
        Inertia.put(route('shows.update', show.id), values).then(() =>
            setSending(false)
        );
    }

    function destroy() {
        if (confirm('Are you sure you want to delete this show?')) {
            Inertia.delete(route('shows.destroy', show.id));
        }
    }

    function restore() {
        if (confirm('Are you sure you want to restore this show?')) {
            Inertia.put(route('shows.restore', show.id));
        }
    }

    return (
        <AuthenticatedLayout>
            <div>
                <Helmet title={`Edit Show`} />
                <h1 className="mb-8 font-bold text-3xl">
                    <Link href={route('shows')} className="text-indigo-600 hover:text-indigo-700">
                        Shows
                    </Link>
                    <span className="text-indigo-600 font-medium mx-2">/</span>
                    Edit Show
                </h1>
                {show.deleted_at && (
                    <TrashedMessage onRestore={restore}>
                        This show has been deleted.
                    </TrashedMessage>
                )}
                <div className="bg-white rounded shadow overflow-hidden max-w-3xl">
                    <form onSubmit={handleSubmit}>
                        <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                            <SelectInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Performance"
                                name="performance_id"
                                errors={errors.performance_id}
                                value={values.performance_id}
                                onChange={handleChange}
                            >
                                <option value="">Select a performance</option>
                                {performances.map((performance) => (
                                    <option key={performance.id} value={performance.id}>
                                        {performance.title}
                                    </option>
                                ))}
                            </SelectInput>
                            <SelectInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Hall"
                                name="hall_id"
                                errors={errors.hall_id}
                                value={values.hall_id}
                                onChange={handleChange}
                            >
                                <option value="">Select Hall</option>
                                {halls.map((hall) => (
                                    <option key={hall.id} value={hall.id}>
                                        Hall {hall.hall_number}
                                    </option>
                                ))}
                            </SelectInput>
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Date & Time"
                                name="datetime"
                                type="datetime-local"
                                errors={errors.datetime}
                                value={values.datetime}
                                onChange={handleChange}
                            />
                            <TextInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Price"
                                name="price"
                                type="number"
                                errors={errors.price}
                                value={values.price}
                                onChange={handleChange}
                            />
                            <SelectInput
                                className="pr-6 pb-8 w-full lg:w-1/2"
                                label="Hall"
                                name="hall_id"
                                errors={errors.hall_id}
                                value={values.hall_id}
                                onChange={handleChange}
                            >
                                <option value="">Select a hall</option>
                                {halls.map((hall) => (
                                    <option key={hall.id} value={hall.id}>
                                        {hall.name}
                                    </option>
                                ))}
                            </SelectInput>
                        </div>
                        <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                            {!show.deleted_at && (
                                <DeleteButton onDelete={destroy}>Delete Show</DeleteButton>
                            )}
                            <LoadingButton
                                loading={sending}
                                type="submit"
                                className="btn-indigo ml-auto"
                            >
                                Update Show
                            </LoadingButton>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};
