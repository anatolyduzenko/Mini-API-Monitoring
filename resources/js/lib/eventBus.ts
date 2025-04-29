import mitt from 'mitt';

type Events = {
    'reload-charts': void;
};

export const eventBus = mitt<Events>();