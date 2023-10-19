<?php 
function create_reqirement_table(){ ?>
    <table>
        <thead>
            <tr>
                <th>アップロード済ファイル</th>
                <th>操作</th>
                <th>新規ファイル</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <?php $outline_data = get_document_path_return(h($_GET['tournament_id']), 'outline') ?>
            <td><?php if(!$outline_data): ?><p>まだアップロードしていません。</p><?php else: ?><a href="<?php echo home_url($outline_data['document_path']) ?>" target="brank">大会要綱</a><?php endif; ?></td>
            <td><input type="submit" id="Delete1" value="削除" <?php if(!get_document_path_tf(h($_GET['tournament_id']), 'outline')){echo 'disabled';}?>></td>
            <td><input type="file" id="fileInput1" name="outline" value="ファイルを選択" onchange="checkFileSize(this)" accept="application/pdf"></td>
            <td><input type="submit" id="Upload1" value="アップロード" disabled></td>
            </tr>
        </tbody>
    </table>
<?php
}

function create_timetable_table(){ ?>
    <table>
        <thead>
            <tr>
                <th>アップロード済ファイル</th>
                <th>操作</th>
                <th>新規ファイル</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <?php $timetable_data = get_document_path_return(h($_GET['tournament_id']), 'timetable') ?>
            <td><?php if(!$timetable_data): ?><p>まだアップロードしていません。</p><?php else: ?><a href="<?php echo home_url($timetable_data['document_path']) ?>" target="brank">日程表</a><?php endif?></td>
            <td><input type="submit" id="Delete2" value="削除" <?php if(!get_document_path_tf(h($_GET['tournament_id']), 'timetable')){echo 'disabled';}?>></td>
            <td><input type="file" id="fileInput2" name="timetable" value="ファイルを選択" onchange="checkFileSize(this)" accept="application/pdf"></td>
            <td><input type="submit" id="Upload2" value="アップロード" disabled></td>
            </tr>
        </tbody>
    </table>
<?php
}