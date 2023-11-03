

// state
export const state = {
    archiveFile: [],
    objectActive: {}
}

// getters
export const getters = {
    archiveFile: state => state.archiveFile,
    objectActive: state => state.objectActive
}

// mutations
export const mutations = {
    archiveFileUpdate(state,file){
        let find = state.archiveFile.find( el => el.id == file.id);
        if(find){
            state.archiveFile.splice(state.archiveFile.findIndex( el => el.id == file.id),1);
        }else if(!find && state.archiveFile.length > 0) {
            state.archiveFile.pop();
            state.archiveFile.push(file);
        }else {
            state.archiveFile.push(file);
        }

        if(state.archiveFile.length > 1){
            state.objectActive = {};
        }else {
            state.objectActive = state.archiveFile[0];
        }
    },
    objectActiveUpdate(state,file){
       if(state.archiveFile.length == 1){
           state.objectActive = file;
       }else {
           state.objectActive = {};
       }
    },
    archiveFileRemove(state,id){
        let find = state.archiveFile.find( el => el.id == id);
        if(find){
            state.archiveFile.splice(state.archiveFile.findIndex( el => el.id == id),1);
        }
    },
    archiveFileEmity(state){
        state.archiveFile = []
    },
    objectActiveEmity(state){
        state.objectActive = {};
    }
};

// actions
export const actions = {};
